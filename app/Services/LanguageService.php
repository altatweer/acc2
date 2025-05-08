<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class LanguageService
{
    /**
     * الجلسة المستخدمة لتخزين اللغة
     */
    const SESSION_KEY = 'locale';
    
    /**
     * اللغات المدعومة في النظام
     */
    const SUPPORTED_LANGUAGES = ['en', 'ar'];
    
    /**
     * اللغة الافتراضية
     */
    const DEFAULT_LANGUAGE = 'en';
    
    /**
     * الحصول على اللغة الحالية
     *
     * @return string
     */
    public static function getCurrentLanguage()
    {
        return App::getLocale();
    }
    
    /**
     * تعيين اللغة
     *
     * @param string $lang اللغة المراد تعيينها
     * @return bool
     */
    public static function setLanguage($lang)
    {
        // التحقق من صحة اللغة
        if (!in_array($lang, self::SUPPORTED_LANGUAGES)) {
            $lang = self::DEFAULT_LANGUAGE;
        }
        
        // تسجيل المعلومات
        Log::debug("Setting language", [
            'language' => $lang,
            'previous_language' => App::getLocale(),
            'previous_session' => Session::get(self::SESSION_KEY),
        ]);
        
        // تعيين اللغة في كل الأماكن المطلوبة
        App::setLocale($lang);
        Config::set('app.locale', $lang);
        Session::put(self::SESSION_KEY, $lang);
        
        // تخزين اللغة في الكوكي لمدة شهر
        $cookieMinutes = 60 * 24 * 30;
        Cookie::queue(self::SESSION_KEY, $lang, $cookieMinutes);
        
        // حفظ الجلسة لضمان تطبيق التغييرات
        if (Session::isStarted()) {
            Session::save();
        }
        
        return true;
    }
    
    /**
     * تهيئة اللغة من الجلسة والكوكي
     *
     * @return string اللغة التي تم تحديدها
     */
    public static function initializeLanguage()
    {
        $lang = self::DEFAULT_LANGUAGE;
        
        // البحث عن اللغة في الجلسة أولاً
        if (Session::has(self::SESSION_KEY) && in_array(Session::get(self::SESSION_KEY), self::SUPPORTED_LANGUAGES)) {
            $lang = Session::get(self::SESSION_KEY);
        } 
        // ثم في الكوكي
        elseif (isset($_COOKIE[self::SESSION_KEY]) && in_array($_COOKIE[self::SESSION_KEY], self::SUPPORTED_LANGUAGES)) {
            $lang = $_COOKIE[self::SESSION_KEY];
        }
        
        // تعيين اللغة
        self::setLanguage($lang);
        
        return $lang;
    }
    
    /**
     * تحديد إذا كانت اللغة الحالية عربية
     *
     * @return bool
     */
    public static function isArabic()
    {
        return self::getCurrentLanguage() === 'ar';
    }
    
    /**
     * تبديل اللغة الحالية (من عربي لإنجليزي أو العكس)
     *
     * @return string اللغة الجديدة
     */
    public static function toggleLanguage()
    {
        $currentLang = self::getCurrentLanguage();
        $newLang = ($currentLang === 'ar') ? 'en' : 'ar';
        
        self::setLanguage($newLang);
        
        return $newLang;
    }
}