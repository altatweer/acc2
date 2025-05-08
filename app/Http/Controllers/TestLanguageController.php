<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class TestLanguageController extends Controller
{
    /**
     * Test page for language switching diagnostics
     */
    public function test()
    {
        $data = [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'cookie_locale' => Cookie::get('locale'),
            'session_id' => Session::getId(),
            'time' => now()->format('Y-m-d H:i:s.u'),
            'cookies' => $_COOKIE,
        ];
        
        Log::debug('Test language page loaded', $data);
        
        return view('test-language', $data);
    }
    
    /**
     * Set language to English for testing
     */
    public function setEnglish(Request $request)
    {
        $locale = 'en';
        
        App::setLocale($locale);
        Session::put('locale', $locale);
        Config::set('app.locale', $locale);
        
        Log::debug('Test set English called', [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale')
        ]);
        
        $cookie = cookie('locale', $locale, 60 * 24 * 30);
        
        return redirect()->route('test.language')
            ->withCookie($cookie)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }
    
    /**
     * Set language to Arabic for testing
     */
    public function setArabic(Request $request)
    {
        $locale = 'ar';
        
        App::setLocale($locale);
        Session::put('locale', $locale);
        Config::set('app.locale', $locale);
        
        Log::debug('Test set Arabic called', [
            'app_locale' => App::getLocale(),
            'session_locale' => Session::get('locale')
        ]);
        
        $cookie = cookie('locale', $locale, 60 * 24 * 30);
        
        return redirect()->route('test.language')
            ->withCookie($cookie)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }
    
    /**
     * Direct force test - returns in chosen language
     */
    public function forceLanguage($lang)
    {
        if (!in_array($lang, ['ar', 'en'])) {
            $lang = 'en';
        }
        
        App::setLocale($lang);
        Session::put('locale', $lang);
        Config::set('app.locale', $lang);
        Session::save();
        
        $content = $lang == 'ar' 
            ? '<div dir="rtl" style="text-align: right">تم تعيين اللغة إلى <strong>العربية</strong></div>' 
            : '<div dir="ltr" style="text-align: left">Language set to <strong>English</strong></div>';
            
        return response($content)
            ->withCookie(cookie('locale', $lang, 60 * 24 * 30))
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }
}
