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
                'description' => 'سداد فاتورة ' . $invoice->invoice_number,
                'recipient_name' => $invoice->invoice_number,
                'created_by' => auth()->id(),
                'invoice_id' => $invoice->id,
            ]);
            $amount = $validated['payment_amount'];
            // إنشاء قيد محاسبي مالي (مدين: الصندوق/البنك، دائن: حساب العميل)
            $lines = [
                [
                    'account_id' => $cashAccount->id,
                    'description' => 'استلام نقد لفاتورة ' . $invoice->invoice_number,
                    'debit' => $amount,
                    'credit' => 0,
                    'currency' => $cashAccount->currency,
                    'exchange_rate' => $validated['exchange_rate'],
                ],
                [
                    'account_id' => $invoice->customer->account_id,
                    'description' => 'تسوية فاتورة ' . $invoice->invoice_number,
                    'debit' => 0,
                    'credit' => $amount,
                    'currency' => $invoice->currency,
                    'exchange_rate' => $validated['exchange_rate'],
                ],
            ];
            $journal = \App\Models\JournalEntry::create([
                'date' => $validated['date'],
                'description' => 'قيد سداد فاتورة ' . $invoice->invoice_number,
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
            // mark invoice paid
            $invoice->status = 'paid';
            $invoice->save();
        });
        return redirect()->route('invoice-payments.create')
            ->with('success', 'تمت معالجة الدفع بنجاح.');
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
