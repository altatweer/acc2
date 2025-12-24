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
       $this->middleware('can:view_vouchers')->only(['index', 'show']);
       $this->middleware('can:add_voucher')->only(['create', 'store']);
       $this->middleware('can:edit_voucher')->only(['edit', 'update']);
       $this->middleware('can:delete_voucher')->only(['destroy']);
       $this->middleware('can:cancel_vouchers')->only(['cancel']);
   }

   public function index(Request $request)
   {
       $query = Voucher::query();
       if (!auth()->user()->can('view_all_vouchers')) {
           $query->where('created_by', auth()->id());
       }
       if ($request->filled('type')) {
           $query->where('type', $request->type);
       }
       if ($request->filled('date')) {
           $query->whereDate('date', $request->date);
       }
       if ($request->filled('recipient_name')) {
           $query->where('recipient_name', 'like', '%' . $request->recipient_name . '%');
       }
       
       // Load journal entry relationships for later filtering and display
       $query->with(['journalEntry.lines.account', 'user']);
       
       // Filter by currency if provided
       if ($request->filled('currency')) {
           $currency = $request->currency;
           // Filter vouchers that have journal entries with this currency
           $query->whereHas('journalEntry.lines', function($q) use ($currency) {
               $q->where('currency', $currency);
           });
       }
       
       $vouchers = $query->latest()->paginate(20);
       
       // Get all unique currencies used in journal entries for the dropdown
       $currencies = DB::table('journal_entry_lines')
           ->select('currency')
           ->distinct()
           ->pluck('currency')
           ->toArray();
       
       return view('vouchers.index', compact('vouchers', 'currencies'));
   }

   public function create(Request $request)
   {
       try {
           // الحصول على جميع العملات المتاحة
           $currencies = Currency::all();
           $defaultCurrency = Currency::where('is_default', true)->first() ?: Currency::first();
           
           // إذا لم توجد عملات، إنشاء عملة افتراضية
           if (!$defaultCurrency) {
               $defaultCurrency = new \stdClass();
               $defaultCurrency->code = 'IQD';
               $defaultCurrency->name = 'Iraqi Dinar';
               $defaultCurrency->is_default = true;
               $defaultCurrency->exchange_rate = 1;
               $defaultCurrency->id = 0;
           }
           
           if ($currencies->isEmpty()) {
               $currencies = collect([$defaultCurrency]);
           }
           
           $user = auth()->user();
           
           // جلب جميع الصناديق النقدية (بغض النظر عن العملة)
           try {
               $isAdmin = $user->isSuperAdmin() || $user->hasRole('admin');
           } catch (\Exception $e) {
               $isAdmin = false;
           }
           
           $cashAccountsQuery = $isAdmin 
               ? Account::where('is_cash_box', 1) 
               : $user->cashBoxes()->where('is_cash_box', 1);
               
           $cashAccounts = $cashAccountsQuery->get();
           
           // جلب جميع الحسابات المستهدفة (بغض النظر عن العملة)
           $targetAccounts = Account::where('is_group', 0)
               ->where('is_cash_box', 0)
               ->get();
           
           // التحقق من وجود حسابات
           if ($cashAccounts->isEmpty()) {
               $cashAccounts = collect([]);
           }
           
           if ($targetAccounts->isEmpty()) {
               $targetAccounts = collect([]);
           }
               
           return view('vouchers.create', compact('currencies', 'defaultCurrency', 'cashAccounts', 'targetAccounts'));
       } catch (\Exception $e) {
           // تسجيل الخطأ
           \Log::error('Error in VoucherController@create: ' . $e->getMessage());
           \Log::error($e->getTraceAsString());
           
           // إنشاء كائنات افتراضية في حالة الخطأ
           $defaultCurrency = new \stdClass();
           $defaultCurrency->code = 'USD';
           $defaultCurrency->name = 'US Dollar';
           $defaultCurrency->is_default = true;
           $defaultCurrency->exchange_rate = 1;
           $defaultCurrency->id = 0;
           
           $currencies = collect([$defaultCurrency]);
           $cashAccounts = collect([]);
           $targetAccounts = collect([]);
           
           return view('vouchers.create', compact('currencies', 'defaultCurrency', 'cashAccounts', 'targetAccounts'));
       }
   }

   public function store(Request $request)
   {
       if (!auth()->check()) {
           return redirect()->route('login')->with('error', __('messages.error_general'));
       }

       $validated = $request->validate([
           'type' => 'required|in:receipt,payment,transfer,deposit,withdraw',
           'date' => 'required|date',
           'recipient_name' => 'nullable|string|max:255',
           'description' => 'required|string|max:1000',
           'transactions' => 'required|array',
           'transactions.*.account_id' => 'required|exists:accounts,id',
           'transactions.*.target_account_id' => 'required|exists:accounts,id',
           'transactions.*.amount' => 'required|numeric|min:0.01',
           'transactions.*.cash_currency' => 'required|string|max:3|exists:currencies,code',
           'transactions.*.target_currency' => 'required|string|max:3|exists:currencies,code',
           'transactions.*.exchange_rate' => 'nullable|numeric|min:0.0000000001',
           'transactions.*.converted_amount' => 'required|numeric|min:0.01',
       ]);

       // التحقق من صحة الحسابات وسعر الصرف
       foreach ($validated['transactions'] as $index => $tx) {
           $cashAccount = Account::find($tx['account_id']);
           $targetAccount = Account::find($tx['target_account_id']);
           
           // التحقق من أن الحسابات موجودة وصحيحة
           if (!$cashAccount || !$cashAccount->is_cash_box) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.account_id" => ["يجب اختيار صندوق نقدي صحيح"]
               ]);
           }
           
           if (!$targetAccount || $targetAccount->is_cash_box) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.target_account_id" => ["يجب اختيار حساب غير نقدي"]
               ]);
           }
           
           // التحقق من سعر الصرف إذا كانت العملات مختلفة
           if ($tx['cash_currency'] !== $tx['target_currency']) {
               if (empty($tx['exchange_rate']) || $tx['exchange_rate'] <= 0) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.exchange_rate" => ["سعر الصرف مطلوب عند اختلاف العملات"]
                   ]);
               }
           }
           
           // التحقق من صحة العملات
           // التحقق من وجود العملات (تجنب استخدام is_active إذا لم يكن موجود)
           try {
               $cashCurrencyExists = Currency::where('code', $tx['cash_currency'])->where('is_active', true)->exists();
               $targetCurrencyExists = Currency::where('code', $tx['target_currency'])->where('is_active', true)->exists();
           } catch (\Exception $e) {
               // إذا لم يكن العمود موجود، تحقق من وجود العملة فقط
               $cashCurrencyExists = Currency::where('code', $tx['cash_currency'])->exists();
               $targetCurrencyExists = Currency::where('code', $tx['target_currency'])->exists();
           }
           
           if (!$cashCurrencyExists) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.cash_currency" => ["عملة الصندوق غير صحيحة أو غير مفعلة"]
               ]);
           }
           
           if (!$targetCurrencyExists) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.target_currency" => ["عملة الحساب غير صحيحة أو غير مفعلة"]
               ]);
           }
       }

       // التحقق من الرصيد للسحب والدفع - استخدام عملة الصندوق
       if (in_array($validated['type'], ['withdraw', 'payment'])) {
           foreach ($validated['transactions'] as $index => $tx) {
               $cashAccount = Account::find($tx['account_id']);
               $cashCurrency = $tx['cash_currency'];
               
               // استخدام دالة balance() من Account model التي تأخذ في الاعتبار طبيعة الحساب
               $currentBalance = $cashAccount->balance($cashCurrency);
               
               // تقريب الرصيد والمبلغ للمقارنة الدقيقة
               $currentBalance = round($currentBalance, 2);
               $requestedAmount = round(floatval($tx['amount']), 2);
               
               \Log::info('Checking cash account balance for withdrawal', [
                   'account_id' => $cashAccount->id,
                   'account_name' => $cashAccount->name,
                   'currency' => $cashCurrency,
                   'current_balance' => $currentBalance,
                   'requested_amount' => $requestedAmount,
                   'account_nature' => $cashAccount->nature
               ]);
               
               if ($currentBalance < $requestedAmount) {
                   throw \Illuminate\Validation\ValidationException::withMessages([
                       "transactions.$index.amount" => ["رصيد الصندوق {$cashAccount->name} لا يكفي للسحب. الرصيد الحالي: " . number_format($currentBalance, 2) . " " . $cashCurrency]
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

       DB::transaction(function () use ($validated, $request) {
           // تحديد العملة الأساسية
           $baseCurrency = Currency::where('is_default', true)->value('code') ?? 'IQD';
           
           $voucher = Voucher::create([
               'voucher_number' => $this->generateVoucherNumber(),
               'type' => $validated['type'],
               'date' => $request->filled('date') ? date('Y-m-d H:i:s', strtotime($validated['date'])) : now(),
               'currency' => 'MIX', // إشارة للعملات المتعددة (3 أحرف فقط)
               'description' => $validated['description'],
               'recipient_name' => $validated['recipient_name'],
               'created_by' => auth()->id(),
           ]);

           // بناء سطور القيد المحاسبي حسب نوع السند مع دعم العملات المتعددة
           $lines = [];
           foreach ($validated['transactions'] as $tx) {
               // جلب بيانات الحسابات
               $cashAccount = Account::find($tx['account_id']);
               $targetAccount = Account::find($tx['target_account_id']);
               
               $cashCurrency = $tx['cash_currency'];
               $targetCurrency = $tx['target_currency'];
               $amount = $tx['amount'];
               $convertedAmount = $tx['converted_amount'];
               $exchangeRate = $tx['exchange_rate'] ?? 1;
               
               // تحديد سعر الصرف لكل سطر
               // السطر بعملة الصندوق: سعر الصرف الافتراضي (1.0 للعملة الأساسية)
               $cashExchangeRate = ($cashCurrency === $baseCurrency) ? 1.0 : (Currency::where('code', $cashCurrency)->value('exchange_rate') ?? 1);
               // السطر بعملة الهدف: استخدام سعر الصرف الذي أدخله المستخدم
               $targetExchangeRate = $exchangeRate;
               
               if (in_array($validated['type'], ['receipt', 'deposit'])) {
                   // سند قبض: الصندوق مدين بعملته، الحساب المستهدف دائن بعملته
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? "قبض من {$targetAccount->name} ({$targetAccount->code})",
                       'debit' => $amount,
                       'credit' => 0,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $cashExchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? "دفع لـ {$cashAccount->name} ({$cashAccount->code})",
                       'debit' => 0,
                       'credit' => $convertedAmount,
                       'currency' => $targetCurrency,
                       'exchange_rate' => $targetExchangeRate,
                   ];
                   
                   // إنشاء الحركات المالية
                   $voucher->transactions()->create([
                       'account_id' => $tx['account_id'],
                       'target_account_id' => $tx['target_account_id'],
                       'amount' => $amount,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $exchangeRate,
                       'date' => $voucher->date,
                       'description' => $tx['description'] ?? null,
                       'type' => 'receipt',
                       'user_id' => auth()->id(),
                   ]);
                   
               } elseif (in_array($validated['type'], ['payment', 'withdraw'])) {
                   // سند صرف: الصندوق دائن بعملته، الحساب المستهدف مدين بعملته
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? "صرف لـ {$targetAccount->name} ({$targetAccount->code})",
                       'debit' => 0,
                       'credit' => $amount,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $cashExchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? "استلام من {$cashAccount->name} ({$cashAccount->code})",
                       'debit' => $convertedAmount,
                       'credit' => 0,
                       'currency' => $targetCurrency,
                       'exchange_rate' => $targetExchangeRate, // استخدام سعر الصرف المدخل من المستخدم
                   ];
                   
                   // إنشاء الحركات المالية
                   $voucher->transactions()->create([
                       'account_id' => $tx['account_id'],
                       'target_account_id' => $tx['target_account_id'],
                       'amount' => -$amount,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $exchangeRate,
                       'date' => $voucher->date,
                       'description' => $tx['description'] ?? null,
                       'type' => 'payment',
                       'user_id' => auth()->id(),
                   ]);
                   
               } elseif ($validated['type'] === 'transfer') {
                   // سند تحويل: الصندوق الأول دائن بعملته، الصندوق الثاني مدين بعملته
                   $lines[] = [
                       'account_id' => $tx['account_id'],
                       'description' => $tx['description'] ?? "تحويل إلى {$targetAccount->name} ({$targetAccount->code})",
                       'debit' => 0,
                       'credit' => $amount,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $cashExchangeRate,
                   ];
                   $lines[] = [
                       'account_id' => $tx['target_account_id'],
                       'description' => $tx['description'] ?? "تحويل من {$cashAccount->name} ({$cashAccount->code})",
                       'debit' => $convertedAmount,
                       'credit' => 0,
                       'currency' => $targetCurrency,
                       'exchange_rate' => $targetExchangeRate, // استخدام سعر الصرف المدخل من المستخدم
                   ];
                   
                   // إنشاء الحركات المالية
                   $voucher->transactions()->create([
                       'account_id' => $tx['account_id'],
                       'target_account_id' => $tx['target_account_id'],
                       'amount' => -$amount,
                       'currency' => $cashCurrency,
                       'exchange_rate' => $exchangeRate,
                       'date' => $voucher->date,
                       'description' => $tx['description'] ?? null,
                       'type' => 'transfer',
                       'user_id' => auth()->id(),
                   ]);
               }
           }
           // حساب المجاميع بالعملة الأساسية لضمان التوازن
           // المهم: يجب التحقق من التوازن باستخدام سعر الصرف الذي أدخله المستخدم
           $totalDebitInBase = 0;
           $totalCreditInBase = 0;
           $baseCurrency = Currency::where('is_default', true)->value('code') ?? 'IQD';
           
           // أولاً: إعادة حساب converted_amount في الخادم بناءً على exchange_rate المدخل
           // هذا يضمن أن converted_amount دائماً صحيح بغض النظر عن ما أرسلته الواجهة الأمامية
           foreach ($validated['transactions'] as $index => &$tx) {
               $cashCurrency = $tx['cash_currency'];
               $targetCurrency = $tx['target_currency'];
               $amount = floatval($tx['amount']);
               $exchangeRate = floatval($tx['exchange_rate'] ?? 1);
               
               if ($cashCurrency !== $targetCurrency) {
                   // تحديد اتجاه التحويل بناءً على exchange_rate المدخل من المستخدم
                   // قاعدة بسيطة: إذا كان exchange_rate > 1، فهذا يعني أن العملة المصدر أقوى من الهدف
                   // مثال: 1 USD = 1450 IQD، exchange_rate = 1450
                   // إذا كان الصندوق USD والهدف IQD: convertedAmount = amount * exchangeRate
                   // إذا كان الصندوق IQD والهدف USD: convertedAmount = amount / exchangeRate
                   
                   // تحديد الاتجاه بناءً على العملات
                   if (($cashCurrency === 'USD' && $targetCurrency === 'IQD') || 
                       ($cashCurrency === 'IQD' && $targetCurrency === 'USD')) {
                       // USD <-> IQD: استخدام قاعدة واضحة
                       if ($cashCurrency === 'USD' && $targetCurrency === 'IQD') {
                           // من USD إلى IQD: الضرب في سعر الصرف
                           $tx['converted_amount'] = round($amount * $exchangeRate, 2);
                       } else {
                           // من IQD إلى USD: القسمة على سعر الصرف
                           $tx['converted_amount'] = round($amount / $exchangeRate, 2);
                       }
                   } else {
                       // عملات أخرى: استخدام exchange_rate من الجدول لتحديد الاتجاه
                       $cashCurrencyModel = Currency::where('code', $cashCurrency)->first();
                       $targetCurrencyModel = Currency::where('code', $targetCurrency)->first();
                       
                       if ($cashCurrencyModel && $targetCurrencyModel) {
                           $isInverse = ($cashCurrencyModel->exchange_rate < $targetCurrencyModel->exchange_rate);
                           
                           if ($isInverse) {
                               $tx['converted_amount'] = round($amount / $exchangeRate, 2);
                           } else {
                               $tx['converted_amount'] = round($amount * $exchangeRate, 2);
                           }
                       } else {
                           // افتراضي: الضرب في سعر الصرف
                           $tx['converted_amount'] = round($amount * $exchangeRate, 2);
                       }
                   }
                   
                   \Log::info('Recalculated converted_amount in voucher', [
                       'transaction_index' => $index,
                       'cash_currency' => $cashCurrency,
                       'target_currency' => $targetCurrency,
                       'amount' => $amount,
                       'exchange_rate' => $exchangeRate,
                       'calculated_converted_amount' => $tx['converted_amount']
                   ]);
               } else {
                   // نفس العملة - يجب أن يكونا متساويين
                   $tx['converted_amount'] = $amount;
               }
           }
           unset($tx); // إزالة المرجع
           
           // الآن حساب المجاميع بالعملة الأساسية
           // المهم: يجب استخدام سعر الصرف الذي أدخله المستخدم للتحويل
           $transactionIndex = 0;
           foreach ($lines as $lineIndex => $line) {
               $lineCurrency = $line['currency'];
               $debit = floatval($line['debit'] ?? 0);
               $credit = floatval($line['credit'] ?? 0);
               
               // تحديد المعاملة التي ينتمي إليها هذا السطر
               // كل معاملة تنتج سطرين (مدين ودائن)
               $txIndex = intval($lineIndex / 2);
               if (isset($validated['transactions'][$txIndex])) {
                   $tx = $validated['transactions'][$txIndex];
                   $txCashCurrency = $tx['cash_currency'];
                   $txTargetCurrency = $tx['target_currency'];
                   $txExchangeRate = floatval($tx['exchange_rate'] ?? 1);
                   
                   // إذا كان السطر بعملة الصندوق
                   if ($lineCurrency === $txCashCurrency) {
                       // تحويل المبلغ بعملة الصندوق إلى العملة الأساسية
                       if ($txCashCurrency === $baseCurrency) {
                           $debitInBase = $debit;
                           $creditInBase = $credit;
                       } else {
                           // المهم: يجب استخدام سعر الصرف المدخل من المستخدم لتحويل عملة الصندوق
                           // لأن المبلغ المحول (convertedAmount) تم حسابه بناءً على exchange_rate المدخل
                           
                           // تحديد اتجاه التحويل بناءً على العملات وسعر الصرف المدخل
                           // قاعدة بسيطة: إذا كان الصندوق USD والهدف IQD: baseAmount = amount * exchangeRate
                           // إذا كان الصندوق IQD والهدف USD: baseAmount = amount / exchangeRate
                           
                           if (($txCashCurrency === 'USD' && $txTargetCurrency === 'IQD') || 
                               ($txCashCurrency === 'IQD' && $txTargetCurrency === 'USD')) {
                               // USD <-> IQD: استخدام قاعدة واضحة
                               if ($txCashCurrency === 'USD' && $txTargetCurrency === 'IQD') {
                                   // من USD إلى IQD (العملة الأساسية): baseAmount = amount * exchangeRate
                                   $debitInBase = $debit * $txExchangeRate;
                                   $creditInBase = $credit * $txExchangeRate;
                               } else {
                                   // من IQD إلى USD: baseAmount = amount / exchangeRate
                                   $debitInBase = $debit / $txExchangeRate;
                                   $creditInBase = $credit / $txExchangeRate;
                               }
                           } else {
                               // عملات أخرى: استخدام exchange_rate من الجدول
                               $cashCurrencyModel = Currency::where('code', $txCashCurrency)->first();
                               $targetCurrencyModel = Currency::where('code', $txTargetCurrency)->first();
                               
                               if ($cashCurrencyModel && $targetCurrencyModel) {
                                   $isInverse = ($cashCurrencyModel->exchange_rate < $targetCurrencyModel->exchange_rate);
                                   
                                   if ($isInverse) {
                                       $debitInBase = $debit * $txExchangeRate;
                                       $creditInBase = $credit * $txExchangeRate;
                                   } else {
                                       $debitInBase = $debit / $txExchangeRate;
                                       $creditInBase = $credit / $txExchangeRate;
                                   }
                               } else {
                                   // افتراضي: الضرب في سعر الصرف
                                   $debitInBase = $debit * $txExchangeRate;
                                   $creditInBase = $credit * $txExchangeRate;
                               }
                           }
                           
                           \Log::info('Converting cash currency to base', [
                               'line_index' => $lineIndex,
                               'debit' => $debit,
                               'credit' => $credit,
                               'cash_currency' => $txCashCurrency,
                               'base_currency' => $baseCurrency,
                               'exchange_rate' => $txExchangeRate,
                               'debit_in_base' => $debitInBase,
                               'credit_in_base' => $creditInBase
                           ]);
                       }
                   }
                   // إذا كان السطر بعملة الهدف
                   elseif ($lineCurrency === $txTargetCurrency) {
                       // تحويل المبلغ بعملة الهدف إلى العملة الأساسية
                       if ($txTargetCurrency === $baseCurrency) {
                           // إذا كانت عملة الهدف هي نفس العملة الأساسية، لا حاجة للتحويل
                           $debitInBase = $debit;
                           $creditInBase = $credit;
                       } else {
                           // إذا كانت عملة الهدف مختلفة عن العملة الأساسية
                           // يجب استخدام سعر الصرف المدخل من المستخدم للتحويل العكسي
                           
                           // تحديد اتجاه التحويل بناءً على كيفية حساب convertedAmount
                           $cashCurrencyModel = Currency::where('code', $txCashCurrency)->first();
                           $targetCurrencyModel = Currency::where('code', $txTargetCurrency)->first();
                           
                           if ($cashCurrencyModel && $targetCurrencyModel) {
                               $isInverse = ($txCashCurrency === 'IQD' && $txTargetCurrency === 'USD') || 
                                           ($cashCurrencyModel->exchange_rate < $targetCurrencyModel->exchange_rate);
                               
                               if ($isInverse) {
                                   // للعملات المقلوبة: convertedAmount = amount / exchangeRate
                                   // للتحويل العكسي إلى العملة الأساسية: baseAmount = convertedAmount * exchangeRate
                                   // لكن إذا كانت العملة الأساسية هي IQD والهدف هو USD، فالمبلغ بالفعل بالعملة الأساسية
                                   // لأن convertedAmount تم حسابه من USD إلى IQD
                                   if ($baseCurrency === $txCashCurrency) {
                                       // العملة الأساسية هي عملة الصندوق، المبلغ بالفعل محول
                                       $debitInBase = $debit * $txExchangeRate;
                                       $creditInBase = $credit * $txExchangeRate;
                                   } else {
                                       // العملة الأساسية هي عملة الهدف، لا حاجة للتحويل
                                       $debitInBase = $debit;
                                       $creditInBase = $credit;
                                   }
                                   
                                   \Log::info('Converting target currency to base (inverse)', [
                                       'line_index' => $lineIndex,
                                       'debit' => $debit,
                                       'credit' => $credit,
                                       'target_currency' => $txTargetCurrency,
                                       'base_currency' => $baseCurrency,
                                       'cash_currency' => $txCashCurrency,
                                       'exchange_rate' => $txExchangeRate,
                                       'debit_in_base' => $debitInBase,
                                       'credit_in_base' => $creditInBase
                                   ]);
                               } else {
                                   // للعملات العادية: convertedAmount = amount * exchangeRate
                                   // للتحويل العكسي إلى العملة الأساسية: baseAmount = convertedAmount / exchangeRate
                                   if ($baseCurrency === $txTargetCurrency) {
                                       // العملة الأساسية هي عملة الهدف، لا حاجة للتحويل
                                       $debitInBase = $debit;
                                       $creditInBase = $credit;
                                   } else {
                                       // العملة الأساسية هي عملة الصندوق، نحتاج للتحويل العكسي
                                       $debitInBase = $debit / $txExchangeRate;
                                       $creditInBase = $credit / $txExchangeRate;
                                   }
                                   
                                   \Log::info('Converting target currency to base (normal)', [
                                       'line_index' => $lineIndex,
                                       'debit' => $debit,
                                       'credit' => $credit,
                                       'target_currency' => $txTargetCurrency,
                                       'base_currency' => $baseCurrency,
                                       'cash_currency' => $txCashCurrency,
                                       'exchange_rate' => $txExchangeRate,
                                       'debit_in_base' => $debitInBase,
                                       'credit_in_base' => $creditInBase
                                   ]);
                               }
                           } else {
                               // حل احتياطي: استخدام CurrencyHelper
                               $debitInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                                   $debit, 
                                   $txTargetCurrency, 
                                   $baseCurrency, 
                                   $validated['date']
                               );
                               $creditInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                                   $credit, 
                                   $txTargetCurrency, 
                                   $baseCurrency, 
                                   $validated['date']
                               );
                           }
                       }
                   }
                   // إذا كانت العملة مختلفة (يجب ألا يحدث هذا)
                   else {
                       // استخدام CurrencyHelper كحل احتياطي
                       if ($lineCurrency === $baseCurrency) {
                           $debitInBase = $debit;
                           $creditInBase = $credit;
                       } else {
                           $debitInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                               $debit, 
                               $lineCurrency, 
                               $baseCurrency, 
                               $validated['date']
                           );
                           $creditInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                               $credit, 
                               $lineCurrency, 
                               $baseCurrency, 
                               $validated['date']
                           );
                       }
                   }
               } else {
                   // حل احتياطي إذا لم نجد المعاملة
                   if ($lineCurrency === $baseCurrency) {
                       $debitInBase = $debit;
                       $creditInBase = $credit;
                   } else {
                       $debitInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                           $debit, 
                           $lineCurrency, 
                           $baseCurrency, 
                           $validated['date']
                       );
                       $creditInBase = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                           $credit, 
                           $lineCurrency, 
                           $baseCurrency, 
                           $validated['date']
                       );
                   }
               }
               
               // تقريب إلى منزلتين عشريتين لتقليل أخطاء التقريب
               $debitInBase = round($debitInBase, 2);
               $creditInBase = round($creditInBase, 2);
               
               $totalDebitInBase += $debitInBase;
               $totalCreditInBase += $creditInBase;
           }
           
           // تقريب المجاميع النهائية
           $totalDebitInBase = round($totalDebitInBase, 2);
           $totalCreditInBase = round($totalCreditInBase, 2);
           
           // تسجيل تفاصيل التحويل للتشخيص
           \Log::info('Voucher balance calculation', [
               'base_currency' => $baseCurrency,
               'total_debit' => $totalDebitInBase,
               'total_credit' => $totalCreditInBase,
               'difference' => abs($totalDebitInBase - $totalCreditInBase),
               'transactions' => $validated['transactions'],
               'lines_count' => count($lines)
           ]);
           
           // التحقق من التوازن مع إصلاح تلقائي للفروقات الصغيرة (أخطاء التقريب)
           $difference = abs($totalDebitInBase - $totalCreditInBase);
           
           // إصلاح تلقائي للفروقات الصغيرة (أقل من 1.00) - أخطاء تقريب طبيعية
           if ($difference > 0.01 && $difference < 1.00) {
               \Log::info('Auto-correcting small rounding difference in voucher', [
                   'difference' => $difference,
                   'base_currency' => $baseCurrency,
                   'total_debit_before' => $totalDebitInBase,
                   'total_credit_before' => $totalCreditInBase
               ]);
               // تصحيح الفرق الصغير بإضافة/طرح من أكبر مبلغ
               if ($totalDebitInBase > $totalCreditInBase) {
                   $totalCreditInBase = $totalDebitInBase;
               } else {
                   $totalDebitInBase = $totalCreditInBase;
               }
               \Log::info('Auto-corrected voucher balance', [
                   'total_debit_after' => $totalDebitInBase,
                   'total_credit_after' => $totalCreditInBase
               ]);
           }
           // إذا كان الفرق كبير (أكبر من 1.00)، نرمي خطأ
           elseif ($difference >= 1.00) {
               // حساب تفصيلي لكل سطر للتشخيص
               $lineDetails = [];
               $txIndex = 0;
               foreach ($lines as $lineIndex => $line) {
                   $txIdx = intval($lineIndex / 2);
                   if (isset($validated['transactions'][$txIdx])) {
                       $tx = $validated['transactions'][$txIdx];
                       $lineDetails[] = [
                           'line_index' => $lineIndex,
                           'transaction_index' => $txIdx,
                           'debit' => $line['debit'],
                           'credit' => $line['credit'],
                           'currency' => $line['currency'],
                           'cash_currency' => $tx['cash_currency'],
                           'target_currency' => $tx['target_currency'],
                           'amount' => $tx['amount'],
                           'converted_amount' => $tx['converted_amount'],
                           'exchange_rate' => $tx['exchange_rate']
                       ];
                   }
               }
               
               \Log::error('Voucher journal entry not balanced', [
                   'base_currency' => $baseCurrency,
                   'total_debit' => $totalDebitInBase,
                   'total_credit' => $totalCreditInBase,
                   'difference' => $difference,
                   'voucher_id' => $voucher->id ?? null,
                   'line_details' => $lineDetails
               ]);
               throw new \Exception("القيد غير متوازن بالعملة الأساسية ($baseCurrency). المدين: " . number_format($totalDebitInBase, 2) . "، الدائن: " . number_format($totalCreditInBase, 2) . " - الفرق: " . number_format($difference, 2));
           }
           
           $journal = \App\Models\JournalEntry::create([
               'date' => $validated['date'],
               'description' => 'قيد سند مالي متعدد العملات #' . $voucher->voucher_number,
               'source_type' => \App\Models\Voucher::class,
               'source_id' => $voucher->id,
               'created_by' => auth()->id(),
               'currency' => 'MIX', // إشارة للعملات المتعددة (3 أحرف فقط)
               'exchange_rate' => 1,
               'total_debit' => $totalDebitInBase,
               'total_credit' => $totalCreditInBase,
           ]);
           
           foreach ($lines as $line) {
               $journal->lines()->create($line);
           }
       });

       return redirect()->route('vouchers.index')->with('success', __('messages.created_success'));
   }

   public function show(Voucher $voucher)
   {
       $user = auth()->user();
       if (!$user->can('view_all_vouchers') && $voucher->created_by !== $user->id) {
           abort(403, 'غير مصرح لك بمشاهدة هذا السند');
       }
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
           if (!$acc || $acc->default_currency !== $validated['currency']) {
               throw \Illuminate\Validation\ValidationException::withMessages([
                   "transactions.$index.account_id" => ["يجب أن تكون عملة حساب الصندوق مطابقة لعملة السند"]
               ]);
           }
           $tgt = \App\Models\Account::find($tx['target_account_id']);
           if (!$tgt || $tgt->default_currency !== $validated['currency']) {
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
       return redirect()->route('vouchers.index')->with('success', __('messages.updated_success'));
   }

   public function cancel(Voucher $voucher)
   {
       if ($voucher->status === 'canceled') {
           return redirect()->back()->with('error', __('messages.error_general'));
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
       return redirect()->route('vouchers.show', ['voucher' => $voucher->id])->with('success', __('messages.deleted_success'));
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
       $user = auth()->user();
       if ($user->isSuperAdmin() || $user->hasRole('admin')) {
           $cashAccountsFrom = \App\Models\Account::where('is_cash_box', 1)->get();
       } else {
           $cashAccountsFrom = $user->cashBoxes()->where('is_cash_box', 1)->get();
       }
       $cashAccountsTo = \App\Models\Account::where('is_cash_box', 1)->get();
       
       // إضافة رصيد كل حساب
       $cashAccountsFrom = $cashAccountsFrom->map(function($account) {
           $account->balance = $account->balance($account->default_currency);
           return $account;
       });
       
       $cashAccountsTo = $cashAccountsTo->map(function($account) {
           $account->balance = $account->balance($account->default_currency);
           return $account;
       });
       
       $currencies = \App\Models\Currency::all();
       $exchangeRates = [];
       foreach ($currencies as $from) {
           foreach ($currencies as $to) {
               if ($from->code !== $to->code) {
                   $exchangeRates[$from->code . '_' . $to->code] = $from->exchange_rate / $to->exchange_rate;
               }
           }
       }
       return view('vouchers.transfer_create', compact('cashAccountsFrom', 'cashAccountsTo', 'exchangeRates'));
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
           'exchange_rate' => 'nullable|numeric|min:0.0000000001', // زيادة الدقة المسموحة
       ]);

       // Debug: سجل البيانات المدخلة
       \Log::info('Transfer Debug - Input Data', [
           'account_id' => $validated['account_id'],
           'target_account_id' => $validated['target_account_id'],
           'amount' => $validated['amount'],
           'exchange_rate' => $validated['exchange_rate'],
           'date' => $validated['date'],
           'raw_request' => $request->all()
       ]);

       $from = \App\Models\Account::find($validated['account_id']);
       $to = \App\Models\Account::find($validated['target_account_id']);
       if (!$from->is_cash_box || !$to->is_cash_box) {
           return back()->withErrors(['account_id' => 'يجب أن يكون الحسابان صناديق كاش.']);
       }
       if ($from->id == $to->id) {
           return back()->withErrors(['target_account_id' => 'لا يمكن التحويل إلى نفس الصندوق.']);
       }

       $fromCurrency = $from->default_currency;
       $toCurrency = $to->default_currency;
       $amountFrom = $validated['amount'];
       $exchangeRate = $validated['exchange_rate'] ?? 1;
       $amountTo = $amountFrom;
       if ($fromCurrency !== $toCurrency) {
           // تحويل عملة: احسب المبلغ المستلم بناءً على سعر الصرف مع دقة عالية
           $amountTo = $exchangeRate ? round($amountFrom * $exchangeRate, 6) : $amountFrom;
       }

       // Debug: سجل تفاصيل العملة والحسابات
       \Log::info('Transfer Debug - Calculation', [
           'from_account' => ['id' => $from->id, 'name' => $from->name, 'currency' => $fromCurrency],
           'to_account' => ['id' => $to->id, 'name' => $to->name, 'currency' => $toCurrency],
           'amount_from' => $amountFrom,
           'exchange_rate' => $exchangeRate,
           'amount_to' => $amountTo,
           'calculation' => $fromCurrency !== $toCurrency ? "$amountFrom * $exchangeRate = $amountTo" : "Same currency"
       ]);

       // التحقق من وجود رصيد كافٍ في الحساب المصدر
       if (!$from->canWithdraw($amountFrom, $fromCurrency)) {
           return back()->withErrors(['amount' => 'لا يوجد رصيد كافٍ في الصندوق المصدر لإجراء التحويل. الرصيد الحالي: ' . $from->balance($fromCurrency) . ' ' . $fromCurrency]);
       }

       \DB::transaction(function () use ($validated, $from, $to, $fromCurrency, $toCurrency, $amountFrom, $amountTo, $exchangeRate) {
           $voucher = new \App\Models\Voucher();
           $voucher->voucher_number = $this->generateVoucherNumber();
           $voucher->type = 'transfer';
           $voucher->date = $validated['date'];
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
               $fromExchangeRate = \App\Models\Currency::where('code', $fromCurrency)->value('exchange_rate') ?? 1;
               
               $journal->lines()->create([
                   'account_id' => $from->id,
                   'debit' => 0,
                   'credit' => $amountFrom,
                   'currency' => $fromCurrency,
                   'exchange_rate' => $fromExchangeRate,
               ]);
               $journal->lines()->create([
                   'account_id' => $to->id,
                   'debit' => $amountFrom,
                   'credit' => 0,
                   'currency' => $toCurrency,
                   'exchange_rate' => $fromExchangeRate,
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
               // تحويل عملة - جلب سعر الصرف لكل عملة
               $fromExchangeRate = \App\Models\Currency::where('code', $fromCurrency)->value('exchange_rate') ?? 1;
               $toExchangeRate = \App\Models\Currency::where('code', $toCurrency)->value('exchange_rate') ?? 1;
               
               $journal->lines()->create([
                   'account_id' => $from->id,
                   'debit' => 0,
                   'credit' => $amountFrom,
                   'currency' => $fromCurrency,
                   'exchange_rate' => $fromExchangeRate,
               ]);
               $journal->lines()->create([
                   'account_id' => $to->id,
                   'debit' => $amountTo,
                   'credit' => 0,
                   'currency' => $toCurrency,
                   'exchange_rate' => $toExchangeRate,
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

       return redirect()->route('vouchers.index', ['type' => 'transfer'])->with('success', __('messages.created_success'));
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

       // Use the new customized print template
       $printSettings = \App\Models\PrintSetting::current();

       // Debug: سجل النتائج في اللوج
       \Log::info('Voucher ID: ' . $voucher->id);
       return view('settings.print-preview-voucher', compact('voucher', 'transactions', 'printSettings'));
   }
}