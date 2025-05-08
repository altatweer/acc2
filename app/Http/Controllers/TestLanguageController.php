<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class TestLanguageController extends Controller
{
    public function setEnglish()
    {
        // فرض اللغة الإنجليزية بطرق متعددة
        config(['app.locale' => 'en']);
        App::setLocale('en');
        Session::put('locale', 'en');
        Session::save();
        
        // حذف الكوكي القديم وإضافة كوكي جديد
        Cookie::queue(Cookie::forget('locale'));
        $cookie = cookie()->forever('locale', 'en');
        
        // طباعة معلومات تصحيح الأخطاء
        echo '<pre>';
        echo "Current app locale: " . App::getLocale() . "\n";
        echo "Current config locale: " . config('app.locale') . "\n";
        echo "Current session locale: " . Session::get('locale') . "\n";
        echo "Locale cookie set to: en\n";
        echo '</pre>';
        
        echo '<a href="/test-language/test">Go to test page</a><br>';
        echo '<a href="/dashboard">Go to dashboard</a>';
        exit;
    }
    
    public function setArabic()
    {
        // إعداد اللغة العربية
        App::setLocale('ar');
        Session::put('locale', 'ar');
        
        // حفظ الجلسة
        Session::save();
        
        // إعداد الكوكي
        $cookie = cookie('locale', 'ar', 60*24*30);
        
        // العودة للصفحة السابقة
        return back()->withCookie($cookie);
    }
    
    public function test()
    {
        // صفحة اختبار بسيطة
        $lang = App::getLocale();
        $sessionLang = Session::get('locale');
        
        // إنشاء HTML بسيط للاختبار
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>اختبار اللغة</title>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .box { border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; }
                .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; margin-right: 10px; }
            </style>
        </head>
        <body>
            <div class="box">
                <h2>معلومات اللغة الحالية</h2>
                <p>اللغة الحالية (App::getLocale): <strong>' . $lang . '</strong></p>
                <p>لغة الجلسة (Session): <strong>' . $sessionLang . '</strong></p>
                <p>كوكي اللغة: <strong>' . (isset($_COOKIE["locale"]) ? $_COOKIE["locale"] : "غير موجود") . '</strong></p>
            </div>
            
            <div class="box">
                <h2>تغيير اللغة</h2>
                <a href="/test-language/set-english" class="btn">English</a>
                <a href="/test-language/set-arabic" class="btn">العربية</a>
            </div>
            
            <div class="box">
                <h2>روابط إضافية</h2>
                <a href="/dashboard" class="btn">لوحة التحكم</a>
                <a href="/test-language/test" class="btn">تحديث هذه الصفحة</a>
            </div>
        </body>
        </html>';
        
        return response($html);
    }
}
