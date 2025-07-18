<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Account;
use App\Services\CustomerAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        return view('customers.create');
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

        try {
            DB::transaction(function () use ($validated) {
                // إنشاء حساب محاسبي أولاً مع اسم مؤقت
                $tempCustomer = (object) $validated; // تحويل البيانات لكائن مؤقت
                $account = CustomerAccountService::createAccountForCustomer($tempCustomer);
                
                // إنشاء العميل مع ربطه بالحساب
                $validated['account_id'] = $account->id;
                $customer = Customer::create($validated);
                
                // تحديث اسم الحساب ليطابق id العميل الفعلي
                $account->update([
                    'name' => $customer->name,
                    'code' => $account->code // الاحتفاظ بنفس الكود
                ]);
            });

            return redirect()->route('customers.index')->with('success', 'تم إنشاء العميل وحسابه المحاسبي بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $balance = $customer->account ? $customer->account->balance() : 0;
        $invoices = $customer->invoices()->orderByDesc('date')->get();
        
        // جلب حركات الحساب المحاسبي
        $accountTransactions = [];
        if ($customer->account) {
            $accountTransactions = $customer->account->journalEntryLines()
                ->with(['journalEntry'])
                ->orderByDesc('created_at')
                ->take(10)
                ->get();
        }
        
        return view('customers.show', compact('customer', 'balance', 'invoices', 'accountTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        // جلب جميع الحسابات المحاسبية للعملاء لاختيار حساب مختلف
        $accounts = Account::where('is_group', false)
            ->where('type', 'asset')
            ->orderBy('name')
            ->get();
            
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
        ]);

        try {
            DB::transaction(function () use ($validated, $customer) {
                $customer->update($validated);
                
                // تحديث اسم الحساب المحاسبي ليطابق اسم العميل
                CustomerAccountService::updateAccountName($customer);
            });

            return redirect()->route('customers.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            // التحقق من إمكانية حذف العميل
            if (!CustomerAccountService::canDeleteCustomer($customer)) {
                return redirect()->route('customers.index')
                    ->with('error', 'لا يمكن حذف هذا العميل لوجود فواتير أو حركات محاسبية مرتبطة به');
            }

            DB::transaction(function () use ($customer) {
                // حذف الحساب المحاسبي إذا كان موجوداً
                if ($customer->account) {
                    $customer->account->delete();
                }
                
                // حذف العميل
                $customer->delete();
            });

            return redirect()->route('customers.index')->with('success', 'تم حذف العميل وحسابه المحاسبي بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'حدث خطأ أثناء حذف العميل: ' . $e->getMessage());
        }
    }
}
