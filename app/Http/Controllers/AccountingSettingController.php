<?php

namespace App\Http\Controllers;

use App\Models\AccountingSetting;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountingSettingController extends Controller
{
    public function edit()
    {
        if (!auth()->user()->can('إدارة إعدادات النظام')) {
            abort(403);
        }
        
        // جلب جميع مجموعات الحسابات (بدون فلترة)
        $accountGroups = Account::where('is_group', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
        // جلب الحسابات الفردية (للمبيعات، المشتريات، الرواتب، إلخ)
        $individualAccounts = Account::where('is_group', false)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
        // جلب الإعدادات الحالية
        $settings = [];
        $settingKeys = [
            'default_sales_account',
            'default_purchases_account', 
            'default_customers_account',
            'default_suppliers_account',
            'salary_expense_account',
            'employee_payables_account',
            'deductions_account',
            'opening_balance_account',  // إضافة حساب الأرصدة الافتتاحية
        ];
        
        foreach ($settingKeys as $key) {
            // نأخذ أول إعداد لهذا المفتاح (لأننا لم نعد نقسم حسب العملة)
            $setting = AccountingSetting::where('key', $key)->first();
            $settings[$key] = $setting ? $setting->value : null;
        }
        
        return view('settings.accounting', compact('settings', 'accountGroups', 'individualAccounts'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->can('إدارة إعدادات النظام')) {
            abort(403);
        }
        
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'sales_account_id' => 'nullable|exists:accounts,id',
            'purchases_account_id' => 'nullable|exists:accounts,id',
            'customers_account_id' => 'nullable|exists:accounts,id',
            'suppliers_account_id' => 'nullable|exists:accounts,id',
            'salary_expense_account_id' => 'nullable|exists:accounts,id',
            'employee_payables_account_id' => 'nullable|exists:accounts,id',
            'deductions_account_id' => 'nullable|exists:accounts,id',
            'opening_balance_account_id' => 'nullable|exists:accounts,id', // إضافة validation
        ]);
        
        // خريطة الحقول إلى مفاتيح الإعدادات
        $settingMap = [
            'sales_account_id' => 'default_sales_account',
            'purchases_account_id' => 'default_purchases_account', 
            'customers_account_id' => 'default_customers_account',
            'suppliers_account_id' => 'default_suppliers_account',
            'salary_expense_account_id' => 'salary_expense_account',
            'employee_payables_account_id' => 'employee_payables_account',
            'deductions_account_id' => 'deductions_account',
            'opening_balance_account_id' => 'opening_balance_account', // إضافة حساب الأرصدة الافتتاحية
        ];
        
        // حفظ الإعدادات
        foreach ($settingMap as $fieldName => $settingKey) {
            $accountId = $validated[$fieldName] ?? null;
            
            if ($accountId) {
                // تحديث أو إنشاء الإعداد
                AccountingSetting::updateOrCreate(
                    ['key' => $settingKey],
                    [
                        'value' => $accountId,
                        'currency' => null, // لا نحدد عملة محددة
                    ]
                );
            } else {
                // حذف الإعداد إذا لم يتم اختيار حساب
                AccountingSetting::where('key', $settingKey)->delete();
            }
        }
        
        return redirect()->back()->with('success', 'تم تحديث إعدادات الحسابات الافتراضية بنجاح.');
    }
} 