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
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'cash_account_id' => 'required|exists:accounts,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'exchange_rate' => 'required|numeric|min:0.000001',
            'date' => 'required|date',
        ]);

        // تحقق من مطابقة العملة
        $invoice = \App\Models\Invoice::findOrFail($validated['invoice_id']);
        $cashAccount = \App\Models\Account::findOrFail($validated['cash_account_id']);
        if ($cashAccount->currency !== $invoice->currency) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cash_account_id' => ['عملة الصندوق يجب أن تطابق عملة الفاتورة.']
            ]);
        }

        // منع السداد إذا كانت الفاتورة ملغية أو مسودة أو مدفوعة بالكامل
        if (!in_array($invoice->status, ['unpaid', 'partial'])) {
            return back()->with('error', __('messages.error_general'));
        }

        // تحقق من عدم تجاوز السداد لإجمالي الفاتورة
        $paidSoFar = \App\Models\Transaction::where('invoice_id', $invoice->id)
            ->where('type', 'receipt')
            ->sum('amount');
        $remaining = $invoice->total - $paidSoFar;
        if ($validated['payment_amount'] > $remaining) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'payment_amount' => ['المبلغ المدفوع يتجاوز المتبقي على الفاتورة.']
            ]);
        }

        DB::transaction(function () use ($validated) {
            $invoice = Invoice::findOrFail($validated['invoice_id']);
            $cashAccount = Account::findOrFail($validated['cash_account_id']);
            // Create receipt voucher
            $voucher = Voucher::create([
                'voucher_number' => $this->generateVoucherNumber(),
                'type' => 'receipt',
                'date' => $validated['date'],
                'currency' => $cashAccount->currency,
                'exchange_rate' => $validated['exchange_rate'],
                'description' => __('messages.invoice_payment_desc', ['number' => $invoice->invoice_number]),
                'recipient_name' => $invoice->invoice_number,
                'created_by' => auth()->id(),
                'invoice_id' => $invoice->id,
            ]);
            $amount = $validated['payment_amount'];
            // إنشاء قيد محاسبي مالي (مدين: الصندوق/البنك، دائن: حساب العملاء الافتراضي حسب عملة الفاتورة)
            $settings = \App\Models\AccountingSetting::where('currency', $invoice->currency)->first();
            $receivablesAccountId = $settings?->receivables_account_id;
            $lines = [
                [
                    'account_id' => $cashAccount->id,
                    'description' => __('messages.invoice_cash_desc', ['number' => $invoice->invoice_number]),
                    'debit' => $amount,
                    'credit' => 0,
                    'currency' => $cashAccount->currency,
                    'exchange_rate' => $validated['exchange_rate'],
                ],
                [
                    'account_id' => $receivablesAccountId,
                    'description' => __('messages.invoice_settle_desc', ['number' => $invoice->invoice_number]),
                    'debit' => 0,
                    'credit' => $amount,
                    'currency' => $invoice->currency,
                    'exchange_rate' => $validated['exchange_rate'],
                ],
            ];
            $journal = \App\Models\JournalEntry::create([
                'date' => $validated['date'],
                'description' => __('messages.invoice_journal_desc', ['number' => $invoice->invoice_number]),
                'source_type' => \App\Models\Voucher::class,
                'source_id' => $voucher->id,
                'created_by' => auth()->id(),
                'currency' => $cashAccount->currency,
                'exchange_rate' => $validated['exchange_rate'],
                'total_debit' => $amount,
                'total_credit' => $amount,
            ]);
            foreach ($lines as $line) {
                $journal->lines()->create($line);
            }
            // إنشاء معاملة مالية (Transaction) تمثل السداد
            \App\Models\Transaction::create([
                'voucher_id' => $voucher->id,
                'invoice_id' => $invoice->id,
                'date' => $validated['date'],
                'type' => 'receipt',
                'account_id' => $cashAccount->id,
                'amount' => $amount,
                'currency' => $invoice->currency,
                'exchange_rate' => $validated['exchange_rate'],
                'description' => __('messages.invoice_payment_desc', ['number' => $invoice->invoice_number]),
                'user_id' => auth()->id(),
            ]);
            // حساب مجموع المدفوعات للفاتورة
            $paidAmount = \App\Models\Transaction::where('invoice_id', $invoice->id)
                ->where('type', 'receipt')
                ->sum('amount');
            if ($paidAmount >= $invoice->total) {
                $invoice->status = 'paid';
            } elseif ($paidAmount > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'unpaid';
            }
            $invoice->save();
        });
        return redirect()->route('invoice-payments.create')
            ->with('success', __('messages.created_success'));
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
