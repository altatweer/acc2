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
        $currencies = \App\Models\Currency::all();
        $accounts = \App\Models\Account::where('is_group', 0)->get();
        // جلب القيم الافتراضية من key/value/currency
        $settings = [];
        $keys = [
            'default_sales_account',
            'default_purchases_account',
            'default_customers_account',
            'default_suppliers_account',
            'salary_expense_account',
            'employee_payables_account',
            'deductions_account',
        ];
        foreach ($currencies as $currency) {
            $row = [];
            foreach ($keys as $key) {
                $setting = \App\Models\AccountingSetting::where('key', $key)->where('currency', $currency->code)->first();
                $row[$key] = $setting ? $setting->value : null;
            }
            $settings[$currency->code] = $row;
        }
        return view('settings.accounting', compact('settings', 'accounts', 'currencies'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->can('إدارة إعدادات النظام')) {
            abort(403);
        }
        $currencies = \App\Models\Currency::all();
        foreach ($currencies as $currency) {
            $validated = $request->validate([
                'sales_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'purchases_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'receivables_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'payables_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'expenses_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'liabilities_account_id.' . $currency->code => 'nullable|exists:accounts,id',
                'deductions_account_id.' . $currency->code => 'nullable|exists:accounts,id',
            ]);
            $data = [
                'currency' => $currency->code,
                'sales_account_id' => $request->input('sales_account_id.' . $currency->code),
                'purchases_account_id' => $request->input('purchases_account_id.' . $currency->code),
                'receivables_account_id' => $request->input('receivables_account_id.' . $currency->code),
                'payables_account_id' => $request->input('payables_account_id.' . $currency->code),
                'expenses_account_id' => $request->input('expenses_account_id.' . $currency->code),
                'liabilities_account_id' => $request->input('liabilities_account_id.' . $currency->code),
                'deductions_account_id' => $request->input('deductions_account_id.' . $currency->code),
            ];
            \App\Models\AccountingSetting::updateOrCreate(['currency' => $currency->code], $data);
        }
        return redirect()->back()->with('success', 'تم تحديث إعدادات الحسابات الافتراضية بنجاح.');
    }
} 