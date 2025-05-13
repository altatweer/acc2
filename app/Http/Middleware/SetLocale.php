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
        // استخدم اللغة الافتراضية من الإعدادات فقط
        $defaultLang = \App\Models\Setting::get('default_language', config('app.locale'));
        \Illuminate\Support\Facades\App::setLocale($defaultLang);
        \Illuminate\Support\Facades\Config::set('app.locale', $defaultLang);
        
        // شارك متغيرات العرض
        view()->share('current_locale', $defaultLang);
        view()->share('dir', $defaultLang == 'ar' ? 'rtl' : 'ltr');
        
        $response = $next($request);
        
        // لطلبات غير API، منع التخزين المؤقت
        if (!$request->expectsJson()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
            if (method_exists($response, 'header')) {
                $response->header('Content-Language', $defaultLang);
            }
        }
        return $response;
    }
} 