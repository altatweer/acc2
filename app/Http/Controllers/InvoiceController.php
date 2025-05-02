<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Voucher;
use App\Models\Item;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $currencies = Currency::all();
        $items = Item::all();
        return view('invoices.create', compact('customers', 'currencies', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number'     => 'nullable|string|unique:invoices,invoice_number',
            'customer_id'        => 'required|exists:customers,id',
            'date'               => 'required|date',
            'total'              => 'required|numeric|min:0',
            'currency'           => 'required|string|exists:currencies,code',
            'items.*.item_id'    => 'required|exists:items,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        // Auto-generate invoice_number if not provided
        if (empty($validated['invoice_number'])) {
            $lastId = Invoice::max('id') ?? 0;
            $validated['invoice_number'] = 'INV-' . str_pad($lastId+1, 5, '0', STR_PAD_LEFT);
        }
        // Set invoice exchange_rate automatically based on selected currency
        $currencyModel = Currency::where('code', $validated['currency'])->first();
        $validated['exchange_rate'] = $currencyModel->exchange_rate;
        $validated['status'] = 'unpaid';
        $validated['created_by'] = auth()->id();

        // Create invoice within DB transaction
        DB::transaction(function() use ($validated) {
            // Persist invoice
            $invoice = Invoice::create($validated);
            // Save invoice items
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $itm) {
                    $lineTotal = $itm['quantity'] * $itm['unit_price'];
                    $invoice->invoiceItems()->create([
                        'item_id'    => $itm['item_id'],
                        'quantity'   => $itm['quantity'],
                        'unit_price' => $itm['unit_price'],
                        'line_total' => $lineTotal,
                    ]);
                }
            }
            // إنشاء قيد محاسبي آجل (مدين: حساب العميل، دائن: حساب الإيرادات)
            $arAccountId = $invoice->customer->account_id;
            $revenueAccount = \App\Models\Account::where('type', 'revenue')->where('is_group', 0)->first();
            if ($revenueAccount) {
                $lines = [
                    [
                        'account_id' => $arAccountId,
                        'description' => 'استحقاق فاتورة ' . $invoice->invoice_number,
                        'debit' => $invoice->total,
                        'credit' => 0,
                        'currency' => $invoice->currency,
                        'exchange_rate' => $invoice->exchange_rate,
                    ],
                    [
                        'account_id' => $revenueAccount->id,
                        'description' => 'إيراد فاتورة ' . $invoice->invoice_number,
                        'debit' => 0,
                        'credit' => $invoice->total,
                        'currency' => $invoice->currency,
                        'exchange_rate' => $invoice->exchange_rate,
                    ],
                ];
                $journal = \App\Models\JournalEntry::create([
                    'date' => $invoice->date,
                    'description' => 'قيد استحقاق فاتورة ' . $invoice->invoice_number,
                    'source_type' => 'invoice',
                    'source_id' => $invoice->id,
                    'created_by' => auth()->id(),
                    'currency' => $invoice->currency,
                    'exchange_rate' => $invoice->exchange_rate,
                    'total_debit' => $invoice->total,
                    'total_credit' => $invoice->total,
                ]);
                foreach ($lines as $line) {
                    $journal->lines()->create($line);
                }
            }
        });
        return redirect()->route('invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // load invoice items and customer
        $invoice->load('invoiceItems.item', 'customer');
        // previous payments
        $payments = Voucher::where('recipient_name', $invoice->invoice_number)
            ->latest()->get();
        // Cash accounts matching the invoice currency
        $cashAccounts = Account::where('is_cash_box', 1)
            ->where('currency', $invoice->currency)
            ->get();
        // available currencies
        $currencies = Currency::all();
        return view('invoices.show', compact('invoice', 'payments', 'cashAccounts', 'currencies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
