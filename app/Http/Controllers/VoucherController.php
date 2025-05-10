<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
   public function __construct()
   {
       $this->middleware('can:عرض السندات')->only(['index', 'show']);
       $this->middleware('can:إضافة سند')->only(['create', 'store']);
       $this->middleware('can:تعديل سند')->only(['edit', 'update']);
       $this->middleware('can:حذف سند')->only(['destroy']);
   }

   public function index(Request $request)
   {
       $query = Voucher::query();

       if ($request->filled('type')) {
           $query->where('type', $request->type);
       }
       if ($request->filled('date')) {
           $query->whereDate('date', $request->date);
       }
       if ($request->filled('recipient_name')) {
           $query->where('recipient_name', 'like', '%' . $request->recipient_name . '%');
       }

       $vouchers = $query->latest()->paginate(20);
       return view('vouchers.index', compact('vouchers'));
   }

   public function create()
   {
       // Default currency for initial selection
       $defaultCurrency = Currency::where('is_default', true)->first();
       $currencies = Currency::all();

       return view('vouchers.create', compact('currencies', 'defaultCurrency'));
   }

   public function store(Request $request)
   {
       if (!auth()->check()) {
           return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لإنشاء السند.');
       }

       $validated = $request->validate([
           'type' => 'required|in:receipt,payment,transfer,deposit,withdraw',
           'date' => 'required|date',
           'currency' => 'required|string|max:3|exists:currencies,code',
           'recipient_name' => 'nullable|string|max:255',
           'description' => 'required|string|max:1000',
           'transactions' => 'required|array',
           'transactions.*.account_id' => 'required|exists:accounts,id',
           'transactions.*.target_account_id' => 'required|exists:accounts,id',
           'transactions.*.amount' => 'required|numeric|min:0.01',
       ]);

       // Ensure account currencies match the voucher currency
       foreach ($validated['transactions'] as $index => $tx) {
           $acc = Account::find($tx['account_id']);
           if (!$acc || $acc->currency !== $validated['currency']) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.account_id" => ["يجب أن تكون عملة حساب الصندوق مطابقة لعملة السند"]
               ]);
           }
           $tgt = Account::find($tx['target_account_id']);
           if (!$tgt || $tgt->currency !== $validated['currency']) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.target_account_id" => ["يجب أن تكون عملة الحساب المستهدف مطابقة لعملة السند"]
               ]);
           }
       }

       // Prevent cash box from going negative on withdrawal/payment
       foreach ($validated['transactions'] as $index => $tx) {
           $acc = Account::find($tx['account_id']);
           if ($acc && $acc->is_cash_box && in_array($validated['type'], ['payment','withdraw','transfer'])) {
               if (!$acc->canWithdraw($tx['amount'])) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.account_id" => ["لا يوجد رصيد كافٍ في الصندوق النقدي لتنفيذ العملية."]
                   ]);
               }
           }
       }

       // Fetch exchange rate for the voucher currency
       $exchangeRate = Currency::where('code', $validated['currency'])->value('exchange_rate');
       // Prevent cash box from going negative on withdrawal/payment
       if (in_array($validated['type'], ['withdraw', 'payment'])) {
           foreach ($validated['transactions'] as $tx) {
               $account = Account::find($tx['account_id']);
               // احتساب الرصيد من القيود المحاسبية فقط
               $currentBalance = $account->journalEntryLines()
                   ->where('currency', $validated['currency'])
                   ->selectRaw('SUM(debit - credit) as balance')
                   ->value('balance') ?? 0;
               if ($currentBalance < $tx['amount']) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       'transactions' => ["رصيد الصندوق {$account->name} لا يكفي للسحب (الرصيد الحالي: {$currentBalance})."]
                   ]);
               }
           }
       }

       // تحقق إضافي لسندات التحويل: الحسابات يجب أن تكون صناديق كاش ومختلفة
       if ($validated['type'] === 'transfer') {
           foreach ($validated['transactions'] as $index => $tx) {
               $acc = Account::find($tx['account_id']);
               $tgt = Account::find($tx['target_account_id']);
               if (!$acc || !$acc->is_cash_box) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.account_id" => ["يجب أن يكون الحساب المحول منه صندوق كاش."]
                   ]);
               }
               if (!$tgt || !$tgt->is_cash_box) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.target_account_id" => ["يجب أن يكون الحساب المحول إليه صندوق كاش."]
                   ]);
               }
               if ($acc->id == $tgt->id) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.target_account_id" => ["لا يمكن التحويل إلى نفس الصندوق."]
                   ]);
               }
           }
       }

       DB::transaction(function () use ($validated, $request, $exchangeRate) {
           $voucher = Voucher::create([
               'voucher_number' => $this->generateVoucherNumber(),
               'type' => $validated['type'],
               'date' => $request->filled('date') ? date('Y-m-d H:i:s', strtotime($validated['date'])) : now(),
               'currency' => $validated['currency'],
               'description' => $validated['description'],
               'recipient_name' => $validated['recipient_name'],
               'created_by' => auth()->id(),
           ]);

           // بناء سطور القيد المحاسبي حسب نوع السند
           $lines = [];
           foreach ($validated['transactions'] as $tx) {
               if (in_array($validated['type'], ['receipt', 'deposit'])) {
                   // قبض: الصندوق/البنك مدين، الحساب المستهدف دائن
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   // إنشاء الحركات المالية
                   $voucher->transactions()->create([
                       'account_id' => $tx['account_id'],
                       'target_account_id' => $tx['target_account_id'],
                       'amount' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                       'date' => $voucher->date,
                       'description' => $tx['description'] ?? null,
                       'type' => 'receipt',
                       'user_id' => auth()->id(),
                   ]);
               } elseif (in_array($validated['type'], ['payment', 'withdraw'])) {
                   // صرف: الصندوق/البنك دائن، الحساب المستفيد مدين
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   // إنشاء الحركات المالية
                   $voucher->transactions()->create([
                       'account_id' => $tx['account_id'],
                       'target_account_id' => $tx['target_account_id'],
                       'amount' => -$tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                       'date' => $voucher->date,
                       'description' => $tx['description'] ?? null,
                       'type' => 'payment',
                       'user_id' => auth()->id(),
                   ]);
               } elseif ($validated['type'] === 'transfer') {
                   // تحويل: الصندوق الأول دائن، الصندوق الثاني مدين
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   // إنشاء الحركات المالية (موجودة مسبقًا في transferStore)
               }
           }
           $totalDebit = collect($lines)->sum('debit');
           $totalCredit = collect($lines)->sum('credit');
           $journal = \App\Models\JournalEntry::create([
               'date' => $validated['date'],
               'description' => 'قيد سند مالي #' . $voucher->voucher_number,
               'source_type' => \App\Models\Voucher::class,
               'source_id' => $voucher->id,
               'created_by' => auth()->id(),
               'currency' => $validated['currency'],
               'exchange_rate' => $exchangeRate,
               'total_debit' => $totalDebit,
               'total_credit' => $totalCredit,
           ]);
           foreach ($lines as $line) {
               $journal->lines()->create($line);
           }
       });

       return redirect()->route('vouchers.index')->with('success', 'تم إنشاء السند بنجاح.');
   }

   public function show(Voucher $voucher)
   {
       $voucher->load('journalEntry.lines.account', 'user');
       return view('vouchers.show', compact('voucher'));
   }

   public function edit(Voucher $voucher)
   {
       $voucher->load('transactions', 'journalEntry.lines');
       $currencies = \App\Models\Currency::all();
       $accounts = \App\Models\Account::all();
       return view('vouchers.edit', compact('voucher', 'currencies', 'accounts'));
   }

   public function update(Request $request, Voucher $voucher)
   {
       $validated = $request->validate([
           'type' => 'required|in:receipt,payment,transfer,deposit,withdraw',
           'date' => 'required|date',
           'currency' => 'required|string|max:3|exists:currencies,code',
           'recipient_name' => 'nullable|string|max:255',
           'description' => 'nullable|string|max:1000',
           'transactions' => 'required|array',
           'transactions.*.account_id' => 'required|exists:accounts,id',
           'transactions.*.target_account_id' => 'required|exists:accounts,id',
           'transactions.*.amount' => 'required|numeric|min:0.01',
       ]);
       // تحقق من مطابقة العملات كما في store
       foreach ($validated['transactions'] as $index => $tx) {
           $acc = \App\Models\Account::find($tx['account_id']);
           if (!$acc || $acc->currency !== $validated['currency']) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.account_id" => ["يجب أن تكون عملة حساب الصندوق مطابقة لعملة السند"]
               ]);
           }
           $tgt = \App\Models\Account::find($tx['target_account_id']);
           if (!$tgt || $tgt->currency !== $validated['currency']) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.target_account_id" => ["يجب أن تكون عملة الحساب المستهدف مطابقة لعملة السند"]
               ]);
           }
       }
       $exchangeRate = \App\Models\Currency::where('code', $validated['currency'])->value('exchange_rate');
       \DB::transaction(function () use ($voucher, $validated, $exchangeRate) {
           // تحديث بيانات السند
           $voucher->update([
               'type' => $validated['type'],
               'date' => $validated['date'],
               'currency' => $validated['currency'],
               'description' => $validated['description'],
               'recipient_name' => $validated['recipient_name'],
           ]);
           // حذف القيود والمعاملات القديمة
           if ($voucher->journalEntry) {
               $voucher->journalEntry->lines()->delete();
               $voucher->journalEntry->delete();
           }
           $voucher->transactions()->delete();
           // إعادة بناء القيود والمعاملات كما في store
           $lines = [];
           foreach ($validated['transactions'] as $tx) {
               if (in_array($validated['type'], ['receipt', 'deposit'])) {
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
               } elseif (in_array($validated['type'], ['payment', 'withdraw'])) {
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
               } elseif ($validated['type'] === 'transfer') {
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => 0,
                       'credit' => $tx['amount'],
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? null,
                       'debit' => $tx['amount'],
                       'credit' => 0,
                       'currency' => $validated['currency'],
                       'exchange_rate' => $exchangeRate,
                   ];
               }
               // إنشاء معاملة مالية جديدة
               $voucher->transactions()->create([
                   'account_id' => $tx['account_id'],
                   'target_account_id' => $tx['target_account_id'],
                   'amount' => $tx['amount'],
                   'currency' => $validated['currency'],
                   'exchange_rate' => $exchangeRate,
                   'date' => $validated['date'],
                   'type' => $validated['type'],
                   'description' => $tx['description'] ?? null,
                   'created_by' => auth()->id(),
               ]);
           }
           $totalDebit = collect($lines)->sum('debit');
           $totalCredit = collect($lines)->sum('credit');
           $journal = \App\Models\JournalEntry::create([
               'date' => $validated['date'],
               'description' => 'قيد سند مالي #' . $voucher->voucher_number,
               'source_type' => \App\Models\Voucher::class,
               'source_id' => $voucher->id,
               'created_by' => auth()->id(),
               'currency' => $validated['currency'],
               'exchange_rate' => $exchangeRate,
               'total_debit' => $totalDebit,
               'total_credit' => $totalCredit,
           ]);
           foreach ($lines as $line) {
               $journal->lines()->create($line);
           }
       });
       return redirect()->route('vouchers.index')->with('success', 'تم تحديث السند بنجاح.');
   }

   public function cancel(Voucher $voucher)
   {
       if ($voucher->status === 'canceled') {
           return redirect()->back()->with('error', 'السند ملغي بالفعل.');
       }
       \DB::transaction(function () use ($voucher) {
           $voucher->update(['status' => 'canceled']);
           // حذف جميع المعاملات المرتبطة بالسند
           $voucher->transactions()->delete();
           // إذا كان السند مرتبطًا بدفعة راتب، أعدها إلى معلقة
           $salaryPayment = \App\Models\SalaryPayment::where('voucher_id', $voucher->id)->first();
           if ($salaryPayment) {
               $salaryPayment->status = 'pending';
               $salaryPayment->payment_date = null;
               $salaryPayment->voucher_id = null;
               $salaryPayment->journal_entry_id = null;
               $salaryPayment->save();
           }
           // تحديث حالة القيد المحاسبي المرتبط
           if ($voucher->journalEntry) {
               $voucher->journalEntry->update(['status' => 'canceled']);
           }
           // توليد قيد عكسي
           if ($voucher->journalEntry) {
               $reverse = $voucher->journalEntry->replicate();
               $reverse->date = now();
               $reverse->description = 'قيد عكسي لإلغاء السند #' . $voucher->voucher_number;
               $reverse->source_type = \App\Models\Voucher::class;
               $reverse->source_id = $voucher->id;
               $reverse->status = 'active';
               $reverse->save();
               foreach ($voucher->journalEntry->lines as $line) {
                   $reverse->lines()->create([
                       'account_id' => $line->account_id,
                       'description' => 'عكس: ' . $line->description,
                       'debit' => $line->credit,
                       'credit' => $line->debit,
                       'currency' => $line->currency,
                       'exchange_rate' => $line->exchange_rate,
                   ]);
               }
           }
           // تحديث حالة الفاتورة إذا كان السند مرتبطًا بفاتورة
           if ($voucher->invoice_id) {
               $invoice = \App\Models\Invoice::find($voucher->invoice_id);
               if ($invoice) {
                   $paid = \App\Models\Transaction::where('invoice_id', $invoice->id)
                       ->where('type', 'receipt')
                       ->sum('amount');
                   if ($paid >= $invoice->total) {
                       $invoice->status = 'paid';
                   } elseif ($paid > 0) {
                       $invoice->status = 'partial';
                   } else {
                       $invoice->status = 'unpaid';
                   }
                   $invoice->save();
               }
           }
       });
       return redirect()->route('vouchers.show', ['voucher' => $voucher->id])->with('success', 'تم إلغاء السند وتوليد قيد عكسي بنجاح.');
   }

   private function generateVoucherNumber()
   {
       $lastId = Voucher::max('id') ?? 0;
       return 'VCH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
   }

   /**
    * عرض صفحة إضافة سند تحويل بين الصناديق فقط
    */
   public function transferCreate()
   {
       $cashAccounts = \App\Models\Account::where('is_cash_box', 1)->get();
       // بناء exchangeRates ديناميكي من جدول العملات
       $currencies = \App\Models\Currency::all();
       $exchangeRates = [];
       foreach ($currencies as $from) {
           foreach ($currencies as $to) {
               if ($from->code !== $to->code) {
                   // سعر الصرف من عملة إلى أخرى = سعر عملة الهدف ÷ سعر عملة المصدر
                   $exchangeRates[$from->code . '_' . $to->code] = $to->exchange_rate / $from->exchange_rate;
               }
           }
       }
       return view('vouchers.transfer_create', compact('cashAccounts', 'exchangeRates'));
   }

   /**
    * تخزين سند تحويل بين الصناديق فقط مع توليد القيود والحركات المالية الصحيحة
    */
   public function transferStore(Request $request)
   {
       $validated = $request->validate([
           'account_id' => 'required|exists:accounts,id',
           'target_account_id' => 'required|exists:accounts,id|different:account_id',
           'amount' => 'required|numeric|min:0.01',
           'date' => 'required|date',
           'description' => 'nullable|string',
           'exchange_rate' => 'nullable|numeric|min:0.0001',
       ]);

       $from = \App\Models\Account::find($validated['account_id']);
       $to = \App\Models\Account::find($validated['target_account_id']);
       if (!$from->is_cash_box || !$to->is_cash_box) {
           return back()->withErrors(['account_id' => 'يجب أن يكون الحسابان صناديق كاش.']);
       }
       if ($from->id == $to->id) {
           return back()->withErrors(['target_account_id' => 'لا يمكن التحويل إلى نفس الصندوق.']);
       }

       $fromCurrency = $from->currency;
       $toCurrency = $to->currency;
       $amountFrom = $validated['amount'];
       $exchangeRate = $validated['exchange_rate'] ?? 1;
       $amountTo = $amountFrom;
       if ($fromCurrency !== $toCurrency) {
           // تحويل عملة: احسب المبلغ المستلم بناءً على سعر الصرف
           $amountTo = $exchangeRate ? $amountFrom / $exchangeRate : $amountFrom;
       }

       \DB::transaction(function () use ($validated, $from, $to, $fromCurrency, $toCurrency, $amountFrom, $amountTo, $exchangeRate) {
           $voucher = new \App\Models\Voucher();
           $voucher->voucher_number = $this->generateVoucherNumber();
           $voucher->type = 'transfer';
           $voucher->date = $validated['date'];
           $voucher->amount = $amountFrom;
           $voucher->description = $validated['description'] ?? null;
           $voucher->account_id = $from->id;
           $voucher->target_account_id = $to->id;
           $voucher->currency = $fromCurrency;
           $voucher->created_by = auth()->id();
           $voucher->status = 'active';
           $voucher->save();

           // توليد قيد محاسبي
           $journal = new \App\Models\JournalEntry();
           $journal->date = $voucher->date;
           $journal->description = 'قيد تحويل بين الصناديق للسند #' . $voucher->voucher_number;
           $journal->source_type = \App\Models\Voucher::class;
           $journal->source_id = $voucher->id;
           $journal->created_by = auth()->id();
           $journal->save();
           $voucher->journal_entry_id = $journal->id;
           $voucher->save();

           if ($fromCurrency === $toCurrency) {
               // تحويل بنفس العملة
               $journal->lines()->create([
                   'account_id' => $from->id,
                   'debit' => 0,
                   'credit' => $amountFrom,
                   'currency' => $fromCurrency,
               ]);
               $journal->lines()->create([
                   'account_id' => $to->id,
                   'debit' => $amountFrom,
                   'credit' => 0,
                   'currency' => $toCurrency,
               ]);
               // الحركات المالية
               $voucher->transactions()->create([
                   'account_id' => $from->id,
                   'amount' => -$amountFrom,
                   'currency' => $fromCurrency,
                   'date' => $voucher->date,
                   'description' => 'تحويل من الصندوق (سند تحويل #' . $voucher->voucher_number . ')',
                   'type' => 'transfer',
                   'user_id' => auth()->id(),
               ]);
               $voucher->transactions()->create([
                   'account_id' => $to->id,
                   'amount' => $amountFrom,
                   'currency' => $toCurrency,
                   'date' => $voucher->date,
                   'description' => 'تحويل إلى الصندوق (سند تحويل #' . $voucher->voucher_number . ')',
                   'type' => 'transfer',
                   'user_id' => auth()->id(),
               ]);
               // بعد إنشاء السطور
               $journal->total_debit = $amountFrom;
               $journal->total_credit = $amountFrom;
           } else {
               // تحويل عملة
               $journal->lines()->create([
                   'account_id' => $from->id,
                   'debit' => 0,
                   'credit' => $amountFrom,
                   'currency' => $fromCurrency,
               ]);
               $journal->lines()->create([
                   'account_id' => $to->id,
                   'debit' => $amountTo,
                   'credit' => 0,
                   'currency' => $toCurrency,
               ]);
               // الحركات المالية
               $voucher->transactions()->create([
                   'account_id' => $from->id,
                   'amount' => -$amountFrom,
                   'currency' => $fromCurrency,
                   'date' => $voucher->date,
                   'description' => 'تحويل من الصندوق (سند تحويل #' . $voucher->voucher_number . ')',
                   'type' => 'transfer',
                   'user_id' => auth()->id(),
               ]);
               $voucher->transactions()->create([
                   'account_id' => $to->id,
                   'amount' => $amountTo,
                   'currency' => $toCurrency,
                   'date' => $voucher->date,
                   'description' => 'تحويل إلى الصندوق (سند تحويل #' . $voucher->voucher_number . ')',
                   'type' => 'transfer',
                   'user_id' => auth()->id(),
               ]);
               // بعد إنشاء السطور
               $journal->total_debit = $amountTo;
               $journal->total_credit = $amountFrom;
           }
           $journal->save();
       });

       return redirect()->route('vouchers.index', ['type' => 'transfer'])->with('success', 'تم إضافة سند التحويل بنجاح.');
   }

   /**
    * طباعة السند
    */
   public function print(Voucher $voucher)
   {
       $voucher = Voucher::with([
           'user',
           'journalEntry.lines.account'
       ])->findOrFail($voucher->id);

       $transactions = \App\Models\Transaction::where('voucher_id', $voucher->id)
           ->with(['account', 'targetAccount'])
           ->orderByDesc('id')
           ->get();

       // Debug: سجل النتائج في اللوج
       \Log::info('Voucher ID: ' . $voucher->id);
       return view('vouchers.print', compact('voucher', 'transactions'));
   }
}