<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Account;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view_customers')->only(['index', 'show']);
        $this->middleware('can:add_customer')->only(['create', 'store']);
        $this->middleware('can:edit_customer')->only(['edit', 'update']);
        $this->middleware('can:delete_customer')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('account')->paginate(20);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Account::where('is_group', false)->get();
        return view('customers.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
        ]);
        // جلب حساب العملاء الافتراضي حسب العملة الافتراضية للنظام
        $defaultCurrency = \App\Models\Currency::where('is_default', true)->first();
        $setting = \App\Models\AccountingSetting::where('currency', $defaultCurrency->code)->first();
        $validated['account_id'] = $setting?->receivables_account_id;
        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', __('messages.created_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $balance = $customer->account ? $customer->account->balance() : 0;
        $invoices = $customer->invoices()->orderByDesc('date')->get();
        return view('customers.show', compact('customer', 'balance', 'invoices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $accounts = Account::where('is_group', false)->get();
        return view('customers.edit', compact('customer', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email,' . $customer->id,
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
            'account_id' => 'required|exists:accounts,id',
        ]);
        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', __('messages.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // منع الحذف إذا كان هناك فواتير أو حركات مالية
        $hasInvoices = $customer->invoices()->exists();
        $hasAccountTransactions = $customer->account && ($customer->account->journalEntryLines()->exists() || $customer->account->transactions()->exists());
        if ($hasInvoices || $hasAccountTransactions) {
            return redirect()->route('customers.index')->with('error', __('messages.error_general'));
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('success', __('messages.deleted_success'));
    }
}
