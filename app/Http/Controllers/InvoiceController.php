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
use Illuminate\Support\Facades\Schema;
use App\Models\Transaction;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view_invoices')->only(['index', 'show']);
        $this->middleware('can:add_invoice')->only(['create', 'store']);
        $this->middleware('can:edit_invoice')->only(['edit', 'update']);
        $this->middleware('can:delete_invoice')->only(['destroy']);
    }

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
        // اجعل الفاتورة الجديدة تبدأ بحالة draft
        $validated['status'] = 'draft';
        $validated['created_by'] = auth()->id();

        // دمج المنتجات المكررة في items
        $mergedItems = [];
        if (isset($validated['items'])) {
            foreach ($validated['items'] as $itm) {
                $key = $itm['item_id'];
                if (!isset($mergedItems[$key])) {
                    $mergedItems[$key] = $itm;
                } else {
                    $mergedItems[$key]['quantity'] += $itm['quantity'];
                }
            }
        }

        // Create invoice within DB transaction
        DB::transaction(function() use ($validated, $mergedItems) {
            // Persist invoice
            $invoice = Invoice::create($validated);
            
            // الحصول على معدل الصرف للعملة المختارة
            $exchangeRate = $validated['exchange_rate'];
            $currency = $validated['currency'];
            
            // Save invoice items
            foreach ($mergedItems as $itm) {
                $lineTotal = $itm['quantity'] * $itm['unit_price'];
                
                // حساب المبلغ بالعملة الأساسية (IQD)
                $baseCurrencyTotal = $lineTotal * $exchangeRate;
                
                // التحقق من وجود الأعمدة قبل الإضافة
                $itemData = [
                    'item_id'    => $itm['item_id'],
                    'quantity'   => $itm['quantity'],
                    'unit_price' => $itm['unit_price'],
                    'line_total' => $lineTotal,
                ];
                
                // إضافة أعمدة العملة المتعددة إذا كانت موجودة
                try {
                    if (Schema::hasColumn('invoice_items', 'currency')) {
                        $itemData['currency'] = $currency;
                    }
                    if (Schema::hasColumn('invoice_items', 'exchange_rate')) {
                        $itemData['exchange_rate'] = $exchangeRate;
                    }
                    if (Schema::hasColumn('invoice_items', 'base_currency_total')) {
                        $itemData['base_currency_total'] = $baseCurrencyTotal;
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء إذا لم تكن الأعمدة موجودة
                }
                
                $invoice->invoiceItems()->create($itemData);
            }
            // لا يتم إنشاء أي قيد محاسبي هنا
        });
        return redirect()->route('invoices.index')->with('success', 'تم إنشاء الفاتورة كمسودة. يمكنك اعتمادها لاحقًا.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        // load invoice items and customer
        $invoice->load('invoiceItems.item', 'customer');
        // إصلاح: البحث عن الدفعات بناءً على invoice_id وليس recipient_name
        $payments = Voucher::where('invoice_id', $invoice->id)
            ->where('type', 'receipt')
            ->with(['transactions', 'journalEntry.lines']) // تحميل العلاقات المطلوبة
            ->latest()->get();
        // Cash accounts matching the invoice currency
        $user = auth()->user();
        if ($user->isSuperAdmin() || $user->hasRole('admin')) {
            $cashAccounts = Account::where('is_cash_box', 1)->get();
        } else {
            $cashAccounts = $user->cashBoxes()
                ->where('is_cash_box', 1)
                ->get();
        }
        
        // إضافة رصيد كل صندوق نقدي
        $cashAccounts = $cashAccounts->map(function($account) {
            $account->balance = $account->balance($account->default_currency);
            return $account;
        });
        
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
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف إلا الفواتير في حالة المسودة فقط.');
        }
        DB::transaction(function() use ($invoice) {
            $invoice->invoiceItems()->delete();
            $invoice->delete();
        });
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة بنجاح.');
    }

    /**
     * اعتماد الفاتورة وتحويلها من مسودة إلى مستحقة الدفع
     */
    public function approve(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'لا يمكن اعتماد فاتورة ليست في حالة مسودة.');
        }
        DB::transaction(function() use ($invoice) {
            $invoice->status = 'unpaid';
            $invoice->save();
            // إنشاء قيد محاسبي آجل (مدين: حساب العميل المحدد، دائن: حساب المبيعات الافتراضي)
            $receivablesAccountId = $invoice->customer->account_id;
            $salesAccountId = \App\Models\AccountingSetting::get('default_sales_account');
            if ($receivablesAccountId && $salesAccountId) {
                $lines = [
                    [
                        'account_id' => $receivablesAccountId,
                        'description' => 'استحقاق فاتورة ' . $invoice->invoice_number,
                        'debit' => $invoice->total,
                        'credit' => 0,
                        'currency' => $invoice->currency,
                        'exchange_rate' => $invoice->exchange_rate,
                    ],
                    [
                        'account_id' => $salesAccountId,
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
        return redirect()->route('invoices.show', $invoice)->with('success', 'تم اعتماد الفاتورة وأصبحت مستحقة الدفع.');
    }

    /**
     * إلغاء الفاتورة إذا كانت غير مدفوعة
     */
    public function cancel(Invoice $invoice)
    {
        if (!in_array($invoice->status, ['unpaid'])) {
            return back()->with('error', 'لا يمكن إلغاء الفاتورة إلا إذا كانت غير مدفوعة بالكامل.');
        }
        DB::transaction(function() use ($invoice) {
            // تحديث الحالة
            $invoice->status = 'canceled';
            $invoice->save();
            // إلغاء القيد المحاسبي المرتبط (soft delete أو توليد قيد عكسي)
            $journal = \App\Models\JournalEntry::where('source_type', 'invoice')->where('source_id', $invoice->id)->first();
            if ($journal && $journal->status !== 'canceled') {
                $journal->update(['status' => 'canceled']);
                // توليد قيد عكسي
                $reverse = $journal->replicate();
                $reverse->date = now();
                $reverse->description = 'قيد عكسي لإلغاء فاتورة #' . $invoice->invoice_number;
                $reverse->status = 'active';
                $reverse->source_type = 'invoice';
                $reverse->source_id = $invoice->id;
                $reverse->created_by = auth()->id();
                $reverse->save();
                foreach ($journal->lines as $line) {
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
        });
        return redirect()->route('invoices.show', $invoice)->with('success', 'تم إلغاء الفاتورة وتوليد قيد عكسي بنجاح.');
    }

    /**
     * طباعة الفاتورة
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('customer', 'invoiceItems.item');
        $payments = $invoice->vouchers()->with('transactions')->get();
        
        // Use the new customized print template
        $printSettings = \App\Models\PrintSetting::current();
        
        return view('settings.print-preview-invoice', compact('invoice', 'payments', 'printSettings'));
    }
}
