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
       $this->middleware('auth'); // ✅ حماية كل العمليات
   }

   public function index()
   {
       $vouchers = Voucher::latest()->paginate(20);
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
           'description' => 'nullable|string|max:1000',
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

       // Fetch exchange rate for the voucher currency
       $exchangeRate = Currency::where('code', $validated['currency'])->value('exchange_rate');
       // Prevent cash box from going negative on withdrawal/payment
       if (in_array($validated['type'], ['withdraw', 'payment'])) {
           foreach ($validated['transactions'] as $tx) {
               $account = Account::find($tx['account_id']);
               // calculate current balance for this currency
               $currentBalance = Transaction::where('account_id', $account->id)
                   ->where('currency', $validated['currency'])
                   ->get()
                   ->reduce(function($carry, $t) {
                       if (in_array($t->type, ['deposit', 'receipt'])) {
                           return $carry + $t->amount;
                       }
                       return $carry - $t->amount;
                   }, 0);
               if ($currentBalance < $tx['amount']) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       'transactions' => ["رصيد الصندوق {$account->name} لا يكفي للسحب (الرصيد الحالي: {$currentBalance})."]
                   ]);
               }
           }
       }

       DB::transaction(function () use ($validated, $request, $exchangeRate) {
           $voucher = Voucher::create([
               'voucher_number' => $this->generateVoucherNumber(),
               'type' => $validated['type'],
               'date' => $validated['date'],
               'currency' => $validated['currency'],
               'description' => $validated['description'],
               'recipient_name' => $validated['recipient_name'],
               'created_by' => auth()->id(),
           ]);

           // بناء سطور القيد المحاسبي
           $lines = [];
           foreach ($validated['transactions'] as $tx) {
               // مدين: حساب الصندوق/البنك، دائن: الحساب المستهدف
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

   private function generateVoucherNumber()
   {
       $lastId = Voucher::max('id') ?? 0;
       return 'VCH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
   }
}