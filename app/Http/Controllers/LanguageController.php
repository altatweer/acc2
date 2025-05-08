<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Services\LanguageService;

class LanguageController extends Controller
{
    /**
     * تبديل لغة النظام
     *
     * @param string $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($lang)
    {
        // تسجيل بداية العملية
        Log::debug("LanguageController::switchLang called", [
            'requested_lang' => $lang,
            'current_lang' => App::getLocale(),
            'session_locale' => Session::get('locale')
        ]);
        
        // تعيين اللغة باستخدام خدمة اللغة
        LanguageService::setLanguage($lang);
        
        // إنشاء معلمة URL لإجبار المتصفح على تحديث الصفحة
        $timestamp = time();
        $previousUrl = url()->previous();
        $redirectUrl = $previousUrl . (parse_url($previousUrl, PHP_URL_QUERY) ? '&' : '?') . "lang_refresh={$lang}_{$timestamp}";
        
        // تسجيل معلومات إعادة التوجيه
        Log::debug("LanguageController redirecting", [
            'from' => $previousUrl,
            'to' => $redirectUrl,
            'new_locale' => App::getLocale()
        ]);
        
        // إعادة توجيه المستخدم مع إضافة كوكي اللغة وتعطيل التخزين المؤقت
        return redirect($redirectUrl)
            ->withCookie(cookie('locale', $lang, 60*24*30)) // 30 days
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->header('Content-Language', $lang);
    }
    
    /**
     * طريقة بديلة للتبديل المباشر للغة - تعمل بشكل موثوق
     * 
     * @param string $lang
     * @return \Illuminate\Http\Response
     */
    public function forceLang($lang)
    {
        // تعيين اللغة
        LanguageService::setLanguage($lang);
        
        // إظهار صفحة تأكيد باللغة المختارة
        $content = $lang == 'ar'
            ? '<div dir="rtl" style="font-family: Tahoma, Arial; text-align: right; padding: 20px;">'
                . '<h1>تم تعيين اللغة إلى العربية</h1>'
                . '<p>تم تغيير لغة النظام إلى اللغة العربية بنجاح.</p>'
                . '<p><a href="/">العودة للصفحة الرئيسية</a></p>'
              . '</div>'
            : '<div dir="ltr" style="font-family: Arial; text-align: left; padding: 20px;">'
                . '<h1>Language set to English</h1>'
                . '<p>The system language has been successfully changed to English.</p>'
                . '<p><a href="/">Return to home page</a></p>'
              . '</div>';
              
        return response($content)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->header('Content-Language', $lang);
    }
}