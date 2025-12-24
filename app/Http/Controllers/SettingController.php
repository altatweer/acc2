<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = [
            'system_name' => Setting::get('system_name', 'نظام الحسابات'),
            'company_name' => Setting::get('company_name', ''),
            'company_logo' => Setting::get('company_logo', ''),
            'default_language' => Setting::get('default_language', 'ar'),
            'balance_calculation_method' => Setting::get('balance_calculation_method', 'account_nature'),
            'enable_invoice_expense_attachment' => Setting::get('enable_invoice_expense_attachment', '0') === '1' || Setting::get('enable_invoice_expense_attachment', false) === true,
        ];
        return view('settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|image|max:2048',
            'default_language' => 'required|in:ar,en',
            'balance_calculation_method' => 'required|in:account_nature,transaction_nature',
            'enable_invoice_expense_attachment' => 'nullable|boolean',
        ]);
        
        Setting::set('system_name', $request->system_name);
        Setting::set('company_name', $request->company_name);
        Setting::set('default_language', $request->default_language);
        Setting::set('balance_calculation_method', $request->balance_calculation_method);
        // Handle checkbox: if checked, value is present, if unchecked, it's not sent
        $enableExpenseAttachment = $request->has('enable_invoice_expense_attachment');
        Setting::set('enable_invoice_expense_attachment', $enableExpenseAttachment ? '1' : '0');
        
        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $logo);
        }
        
        // امسح اللغة من الجلسة ليتم تطبيق اللغة الافتراضية فورًا
        Session::forget('locale');
        
        // Save settings message to show in the next request
        $message = app()->getLocale() == 'ar' ? 'تم تحديث إعدادات النظام بنجاح' : 'System settings updated successfully';
        
        // Apply the language change if needed
        $newLang = $request->default_language;
        $currentLang = App::getLocale();
        
        // If language changed, redirect إلى نفس الصفحة بدون Prefix لغة
        if ($newLang != $currentLang) {
            return redirect('/settings/system')->with('success', $message);
        }
        
        return redirect()->back()->with('success', $message);
    }
} 