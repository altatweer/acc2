<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view_suppliers')->only(['index', 'show']);
        $this->middleware('can:add_supplier')->only(['create', 'store']);
        $this->middleware('can:edit_supplier')->only(['edit', 'update']);
        $this->middleware('can:delete_supplier')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::with('account')->paginate(20);
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:suppliers,email',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // إنشاء المورد أولاً
                $supplier = Supplier::create($validated);
                
                // إنشاء حساب محاسبي منفصل للمورد
                $account = SupplierAccountService::createAccountForSupplier($supplier);
                
                // ربط المورد بالحساب
                $supplier->update(['account_id' => $account->id]);
            });

            return redirect()->route('suppliers.index')->with('success', 'تم إنشاء المورد وحسابه المحاسبي بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $balance = $supplier->account ? $supplier->account->balance() : 0;
        
        // جلب حركات الحساب المحاسبي
        $accountTransactions = [];
        if ($supplier->account) {
            $accountTransactions = $supplier->account->journalEntryLines()
                ->with(['journalEntry'])
                ->orderByDesc('created_at')
                ->take(10)
                ->get();
        }
        
        return view('suppliers.show', compact('supplier', 'balance', 'accountTransactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $supplier) {
                $supplier->update($validated);
                
                // تحديث اسم الحساب المحاسبي ليطابق اسم المورد
                SupplierAccountService::updateAccountName($supplier);
            });

            return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // التحقق من إمكانية حذف المورد
            if (!SupplierAccountService::canDeleteSupplier($supplier)) {
                return redirect()->route('suppliers.index')
                    ->with('error', 'لا يمكن حذف هذا المورد لوجود فواتير أو حركات محاسبية مرتبطة به');
            }

            DB::transaction(function () use ($supplier) {
                // حذف الحساب المحاسبي إذا كان موجوداً
                if ($supplier->account) {
                    $supplier->account->delete();
                }
                
                // حذف المورد
                $supplier->delete();
            });

            return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد وحسابه المحاسبي بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('suppliers.index')->with('error', 'حدث خطأ أثناء حذف المورد: ' . $e->getMessage());
        }
    }
} 