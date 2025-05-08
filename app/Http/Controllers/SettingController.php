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
        ]);
        
        Setting::set('system_name', $request->system_name);
        Setting::set('company_name', $request->company_name);
        Setting::set('default_language', $request->default_language);
        
        if ($request->hasFile('company_logo')) {
            $logo = $request->file('company_logo')->store('logos', 'public');
            Setting::set('company_logo', $logo);
        }
        
        // Apply the language change if needed
        $newLang = $request->default_language;
        $currentLang = App::getLocale();
        
        // Save settings message to show in the next request
        $message = app()->getLocale() == 'ar' ? 'تم تحديث إعدادات النظام بنجاح' : 'System settings updated successfully';
        
        // If language changed, redirect to the same page with new language
        if ($newLang != $currentLang) {
            // Create a redirect to the settings page with the new language
            $url = '/' . $newLang . '/settings/system';
            return Redirect::to($url)->with('success', $message);
        }
        
        return redirect()->back()->with('success', $message);
    }
} 