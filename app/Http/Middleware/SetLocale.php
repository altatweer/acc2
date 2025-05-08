<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Services\LanguageService;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::debug("SetLocale middleware started", [
            'session_locale' => session('locale'),
            'cookie_locale' => $request->cookie('locale'),
            'current_locale' => App::getLocale(),
            'lang_refresh' => $request->query('lang_refresh')
        ]);
        
        // التحقق من وجود معلمة تحديث اللغة (أعلى أولوية)
        if ($request->has('lang_refresh')) {
            $langParam = $request->query('lang_refresh');
            if (preg_match('/^(ar|en)_/', $langParam, $matches)) {
                $lang = $matches[1];
                LanguageService::setLanguage($lang);
            }
        }
        // في حالة عدم وجود معلمة، تهيئة اللغة من الجلسة/الكوكي
        else {
            LanguageService::initializeLanguage();
        }
        
        // تحديد متغيرات العرض المشتركة لجميع القوالب
        view()->share('current_locale', App::getLocale());
        view()->share('dir', App::getLocale() == 'ar' ? 'rtl' : 'ltr');
        
        Log::debug("SetLocale middleware completed", [
            'final_locale' => App::getLocale(),
            'session_locale' => session('locale')
        ]);
        
        $response = $next($request);
        
        // لطلبات غير API، منع تخزين الاستجابة مؤقتًا
        if (!$request->expectsJson()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            
            // إضافة معلومات اللغة للعنوان
            if (method_exists($response, 'header')) {
                $response->header('Content-Language', App::getLocale());
            }
        }
        
        return $response;
    }
} 