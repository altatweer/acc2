<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

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
     * تعيين اللغة (تستخدم فقط من الإعدادات بواسطة الأدمن)
     *
     * @param string $lang
     * @return bool
     */
    public static function setLanguage($lang)
    {
        // التحقق من صحة اللغة
        if (!in_array($lang, self::SUPPORTED_LANGUAGES)) {
            $lang = self::DEFAULT_LANGUAGE;
        }
        // حفظ اللغة في الإعدادات
        Setting::set('default_language', $lang);
        App::setLocale($lang);
        Config::set('app.locale', $lang);
        return true;
    }
    
    /**
     * تهيئة اللغة من الإعدادات فقط
     *
     * @return string
     */
    public static function initializeLanguage()
    {
        $lang = Setting::get('default_language', self::DEFAULT_LANGUAGE);
        App::setLocale($lang);
        Config::set('app.locale', $lang);
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