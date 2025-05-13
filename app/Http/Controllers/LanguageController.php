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

    /**
     * عرض قائمة اللغات المتوفرة
     */
    public function index()
    {
        $langPath = resource_path('lang');
        $languages = [];
        foreach (scandir($langPath) as $dir) {
            if ($dir === '.' || $dir === '..' || !is_dir($langPath . DIRECTORY_SEPARATOR . $dir)) continue;
            $messagesFile = $langPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'messages.php';
            $languages[] = [
                'code' => $dir,
                'has_messages' => file_exists($messagesFile),
            ];
        }
        return view('languages.index', compact('languages'));
    }

    /**
     * عرض نموذج رفع ملف لغة جديدة
     */
    public function uploadForm()
    {
        return view('languages.upload');
    }

    /**
     * معالجة رفع ملف لغة جديدة
     */
    public function upload(Request $request)
    {
        $request->validate([
            'code' => 'required|string|alpha|size:2',
            'messages' => 'required|file|mimes:php',
        ]);
        $code = strtolower($request->code);
        $langDir = resource_path('lang/' . $code);
        if (!is_dir($langDir)) {
            mkdir($langDir, 0775, true);
        }
        $file = $request->file('messages');
        // تحقق أن الملف يحتوي على مصفوفة PHP فقط
        $content = file_get_contents($file->getRealPath());
        if (strpos($content, 'return [') === false) {
            return back()->withErrors(['messages' => 'ملف اللغة غير صالح. يجب أن يكون مصفوفة PHP تبدأ بـ return [']);
        }
        move_uploaded_file($file->getRealPath(), $langDir . '/messages.php');
        return redirect()->route('languages.index')->with('success', 'تم رفع اللغة بنجاح.');
    }

    /**
     * تحميل ملف لغة موجودة
     */
    public function download($code)
    {
        $file = resource_path('lang/' . $code . '/messages.php');
        if (!file_exists($file)) {
            abort(404);
        }
        return response()->download($file, $code . '_messages.php');
    }
}