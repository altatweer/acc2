<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Voucher;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function create(Request $request)
    {
        $invoices = Invoice::where('status', 'unpaid')->get();
        // load cash accounts; will filter in JS by invoice currency
        $cashAccounts = Account::where('is_cash_box', 1)->get();
        $selectedInvoice = $request->query('invoice_id');
        return view('invoice_payments.create', compact('invoices', 'cashAccounts', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        // Debugging - طباعة البيانات المرسلة
        \Log::info('Invoice Payment Request Data:', $request->all());
        
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'cash_account_id' => 'required|exists:accounts,id',
            'payment_currency' => 'required|exists:currencies,code',
            'payment_amount' => 'required|numeric|min:0.01',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'date' => 'required|date',
        ]);

        \Log::info('Validated Data:', $validated);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $cashAccount = Account::findOrFail($validated['cash_account_id']);
        $paymentCurrency = $validated['payment_currency'];

        // منع السداد إذا كانت الفاتورة ملغية أو مسودة أو مدفوعة بالكامل
        if (!in_array($invoice->status, ['unpaid', 'partial'])) {
            return back()->with('error', 'لا يمكن السداد على هذه الفاتورة في الحالة الحالية.');
        }

        // حساب المبلغ المحول إلى عملة الفاتورة
        $paymentAmount = $validated['payment_amount']; // المبلغ بعملة السداد المختارة
        $exchangeRate = $validated['exchange_rate'];
        
        // استخدام CurrencyHelper للتحويل الصحيح
        if ($paymentCurrency === $invoice->currency) {
            // نفس العملة - لا حاجة للتحويل
            $convertedAmount = $paymentAmount;
            $exchangeRate = 1.0;
        } else {
            // عملات مختلفة - استخدام CurrencyHelper للتحويل مع السعر التاريخي
            $convertedAmount = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                $paymentAmount,
                $paymentCurrency,
                $invoice->currency,
                $validated['date']
            );
        }

        \Log::info('Conversion calculation:', [
            'payment_currency' => $paymentCurrency,
            'invoice_currency' => $invoice->currency,
            'payment_amount' => $paymentAmount,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount
        ]);

        // تحقق من عدم تجاوز السداد لإجمالي الفاتورة
        // حساب المدفوع من جميع السندات المرتبطة بالفاتورة
        // تحويل جميع المبالغ إلى عملة الفاتورة قبل الجمع
        $paidSoFar = 0;
        $previousTransactions = \App\Models\Transaction::where('invoice_id', $invoice->id)
            ->where('type', 'receipt')
            ->get();
        
        foreach ($previousTransactions as $prevTransaction) {
            if ($prevTransaction->currency === $invoice->currency) {
                // نفس العملة - جمع مباشرة
                $paidSoFar += $prevTransaction->amount;
            } else {
                // عملات مختلفة - تحويل إلى عملة الفاتورة
                $convertedPrevAmount = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                    $prevTransaction->amount,
                    $prevTransaction->currency,
                    $invoice->currency,
                    $prevTransaction->date
                );
                $paidSoFar += $convertedPrevAmount;
            }
        }
        
        $paidSoFar = round($paidSoFar, 2);
        $remaining = $invoice->total - $paidSoFar;
        
        \Log::info('Payment validation:', [
            'invoice_total' => $invoice->total,
            'paid_so_far' => $paidSoFar,
            'remaining' => $remaining,
            'converted_amount' => $convertedAmount,
            'will_exceed' => $convertedAmount > $remaining
        ]);
        
        if ($convertedAmount > $remaining) {
            \Log::error('Payment exceeds remaining amount');
            return back()->withInput()->withErrors([
                'payment_amount' => 'المبلغ المحول (' . number_format($convertedAmount, 2) . ' ' . $invoice->currency . ') يتجاوز المتبقي على الفاتورة (' . number_format($remaining, 2) . ' ' . $invoice->currency . ').'
            ]);
        }

        // ملاحظة: لا نتحقق من رصيد الصندوق في فواتير المبيعات
        // لأن العميل يدفع لنا (المال يدخل للصندوق وليس يخرج منه)

        \Log::info('Starting database transaction...');

        try {
            DB::transaction(function () use ($validated, $invoice, $cashAccount, $paymentAmount, $convertedAmount, $exchangeRate, $paymentCurrency) {
                \Log::info('Inside transaction - creating voucher...');
                
                // Create receipt voucher
                $voucher = Voucher::create([
                    'voucher_number' => $this->generateVoucherNumber(),
                    'type' => 'receipt',
                    'date' => $validated['date'],
                    'currency' => $paymentCurrency,
                    'exchange_rate' => $exchangeRate,
                    'description' => 'سداد فاتورة رقم ' . $invoice->invoice_number,
                    'recipient_name' => $invoice->customer->name,
                    'created_by' => auth()->id(),
                    'invoice_id' => $invoice->id,
                ]);

                \Log::info('Voucher created successfully', ['voucher_id' => $voucher->id]);

                // إنشاء قيد محاسبي مالي
                $receivablesAccountId = $invoice->customer->account_id;
                
                \Log::info('Creating journal entry lines...', [
                    'cash_account_id' => $cashAccount->id,
                    'receivables_account_id' => $receivablesAccountId,
                    'payment_amount' => $paymentAmount,
                    'converted_amount' => $convertedAmount
                ]);
                
                $lines = [
                    [
                        'account_id' => $cashAccount->id,
                        'description' => 'تحصيل نقدي من فاتورة ' . $invoice->invoice_number,
                        'debit' => $paymentAmount,
                        'credit' => 0,
                        'currency' => $paymentCurrency,
                        'exchange_rate' => $paymentCurrency === $invoice->currency ? 1.0 : $exchangeRate,
                    ],
                    [
                        'account_id' => $receivablesAccountId,
                        'description' => 'سداد مستحقات فاتورة ' . $invoice->invoice_number,
                        'debit' => 0,
                        'credit' => $convertedAmount,
                        'currency' => $invoice->currency,
                        'exchange_rate' => 1.0, // القيد بعملة الفاتورة الأصلية
                    ],
                ];
                
                // إنشاء القيد المحاسبي
                $journalCurrency = $paymentCurrency === $invoice->currency ? $paymentCurrency : 'MIX';
                $isMultiCurrency = $paymentCurrency !== $invoice->currency;
                
                // تحويل المبلغ المدفوع إلى عملة الفاتورة للتحقق من التوازن
                $debitInInvoiceCurrency = $paymentCurrency === $invoice->currency 
                    ? $paymentAmount 
                    : \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                        $paymentAmount, 
                        $paymentCurrency, 
                        $invoice->currency, 
                        $validated['date']
                    );
                
                $creditInInvoiceCurrency = $convertedAmount; // بالفعل بعملة الفاتورة
                
                // تقريب المبالغ المحولة
                $debitInInvoiceCurrency = round($debitInInvoiceCurrency, 2);
                $creditInInvoiceCurrency = round($creditInInvoiceCurrency, 2);
                
                // التحقق من التوازن (فرق مقبول 0.10 لأخطاء التقريب في التحويلات)
                $difference = abs($debitInInvoiceCurrency - $creditInInvoiceCurrency);
                if ($difference > 0.10) {
                    \Log::error('Invoice payment journal entry not balanced', [
                        'debit_in_invoice_currency' => $debitInInvoiceCurrency,
                        'credit_in_invoice_currency' => $creditInInvoiceCurrency,
                        'difference' => $difference,
                        'invoice_id' => $invoice->id
                    ]);
                    throw new \Exception("القيد غير متوازن مالياً. المدين: " . number_format($debitInInvoiceCurrency, 2) . " " . $invoice->currency . "، الدائن: " . number_format($creditInInvoiceCurrency, 2) . " " . $invoice->currency . " - الفرق: " . number_format($difference, 2));
                }
                
                // إذا كان الفرق صغير (أقل من 0.10)، نصححه تلقائياً
                if ($difference > 0.01 && $difference <= 0.10) {
                    \Log::info('Auto-correcting small rounding difference in invoice payment', [
                        'difference' => $difference,
                        'invoice_currency' => $invoice->currency
                    ]);
                    if ($debitInInvoiceCurrency > $creditInInvoiceCurrency) {
                        $creditInInvoiceCurrency = $debitInInvoiceCurrency;
                    } else {
                        $debitInInvoiceCurrency = $creditInInvoiceCurrency;
                    }
                }
                
                $journal = \App\Models\JournalEntry::create([
                    'date' => $validated['date'],
                    'description' => 'قيد سداد فاتورة ' . $invoice->invoice_number,
                    'source_type' => \App\Models\Voucher::class,
                    'source_id' => $voucher->id,
                    'created_by' => auth()->id(),
                    'currency' => $journalCurrency,
                    'is_multi_currency' => $isMultiCurrency,
                    'exchange_rate' => $exchangeRate,
                    'total_debit' => $debitInInvoiceCurrency,   // ✅ بنفس العملة (عملة الفاتورة)
                    'total_credit' => $creditInInvoiceCurrency, // ✅ بنفس العملة (عملة الفاتورة)
                ]);
                
                foreach ($lines as $line) {
                    $journal->lines()->create($line);
                }
                
                \Log::info('Journal entry created successfully', ['journal_id' => $journal->id]);
                
                // إنشاء معاملة مالية (Transaction) تمثل السداد
                $transaction = \App\Models\Transaction::create([
                    'voucher_id' => $voucher->id,
                    'invoice_id' => $invoice->id,
                    'date' => $validated['date'],
                    'type' => 'receipt',
                    'account_id' => $cashAccount->id,
                    'amount' => $convertedAmount, // المبلغ بعملة الفاتورة
                    'currency' => $invoice->currency,
                    'exchange_rate' => $exchangeRate,
                    'description' => 'سداد فاتورة ' . $invoice->invoice_number,
                    'user_id' => auth()->id(),
                ]);
                
                \Log::info('Transaction created successfully', ['transaction_id' => $transaction->id]);
                
                // تحديث حالة الفاتورة
                // حساب المدفوع من جميع السندات المرتبطة بالفاتورة
                // تحويل جميع المبالغ إلى عملة الفاتورة قبل الجمع
                $paidAmount = 0;
                $allTransactions = \App\Models\Transaction::where('invoice_id', $invoice->id)
                    ->where('type', 'receipt')
                    ->get();
                
                foreach ($allTransactions as $paymentTransaction) {
                    if ($paymentTransaction->currency === $invoice->currency) {
                        // نفس العملة - جمع مباشرة
                        $paidAmount += $paymentTransaction->amount;
                    } else {
                        // عملات مختلفة - تحويل إلى عملة الفاتورة
                        $convertedPaymentAmount = \App\Helpers\CurrencyHelper::convertWithHistoricalRate(
                            $paymentTransaction->amount,
                            $paymentTransaction->currency,
                            $invoice->currency,
                            $paymentTransaction->date
                        );
                        $paidAmount += $convertedPaymentAmount;
                    }
                }
                
                // تقريب المبلغ المدفوع
                $paidAmount = round($paidAmount, 2);
                    
                \Log::info('Updating invoice status...', [
                    'total_paid_amount' => $paidAmount,
                    'invoice_total' => $invoice->total,
                    'previous_status' => $invoice->status
                ]);
                    
                // التحقق من السداد الكامل (مع هامش صغير لأخطاء التقريب)
                if (abs($paidAmount - $invoice->total) < 0.01 || $paidAmount >= $invoice->total) {
                    $invoice->status = 'paid';
                } elseif ($paidAmount > 0) {
                    $invoice->status = 'partial';
                } else {
                    $invoice->status = 'unpaid';
                }
                $invoice->save();
                
                \Log::info('Invoice status updated', ['new_status' => $invoice->status]);
            });
        } catch (\Exception $e) {
            \Log::error('Database transaction failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->withErrors(['payment_amount' => 'حدث خطأ أثناء عملية السداد: ' . $e->getMessage()]);
        }

        \Log::info('Database transaction completed successfully');

        $successMessage = 'تم السداد بنجاح. المبلغ المدفوع: ' . number_format($paymentAmount, 2) . ' ' . $paymentCurrency;
        if ($paymentCurrency !== $invoice->currency) {
            $successMessage .= ' (المحول: ' . number_format($convertedAmount, 2) . ' ' . $invoice->currency . ')';
        }

        \Log::info('Redirecting with success message', ['message' => $successMessage]);

        return redirect()->route('invoices.show', $invoice)->with('success', $successMessage);
    }

    /**
     * Generate a unique voucher number.
     */
    private function generateVoucherNumber()
    {
        $lastId = Voucher::max('id') ?? 0;
        return 'VCH-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}
