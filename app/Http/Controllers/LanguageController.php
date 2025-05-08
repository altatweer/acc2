<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;

class LanguageController extends Controller
{
    /**
     * Switch language method
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request)
    {
        $lang = $request->input('lang');
        
        Log::debug("Language switch REQUEST", [
            'requested_lang' => $lang,
            'current_lang' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'url' => $request->url(),
            'referer' => $request->headers->get('referer')
        ]);
        
        // تأكد من أن اللغة صالحة
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }
        
        // حالة خاصة إذا كان المطلوب هو الإنجليزية
        if ($lang === 'en') {
            Log::debug("ENGLISH language requested specifically");
        }
        
        // مسح قيم اللغة السابقة وتعيين القيم الجديدة
        Session::forget('locale');
        Config::set('app.locale', $lang);
        App::setLocale($lang);
        Session::put('locale', $lang);
        
        // تعيين الكوكيز
        Cookie::queue('locale', $lang, 60*24*30);
        
        // لضمان حفظ التغييرات
        Session::save();
        
        // تسجيل النتيجة
        Log::debug("Language switch RESULT", [
            'new_locale' => App::getLocale(), 
            'session_locale' => Session::get('locale'),
            'config_locale' => Config::get('app.locale')
        ]);
        
        // الحصول على عنوان URL للإعادة إليه
        $redirectUrl = $request->headers->get('referer');
        if (!$redirectUrl) {
            $redirectUrl = url('/dashboard');
        }
        
        // تنظيف العنوان من البارامترات القديمة
        $baseUrl = strtok($redirectUrl, '?');
        $redirectUrl = $baseUrl . '?lang=' . $lang . '&t=' . time();
        
        Log::debug("Language switch REDIRECT", [
            'redirect_to' => $redirectUrl
        ]);
        
        // إعادة التوجيه مع ترويسات منع التخزين المؤقت
        return Redirect::to($redirectUrl)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Wed, 11 Jan 1984 05:00:00 GMT')
            ->withCookie('locale', $lang, 60*24*30);
    }
}