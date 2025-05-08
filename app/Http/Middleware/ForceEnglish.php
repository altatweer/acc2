<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ForceEnglish
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // تسجيل بداية العملية
        Log::info('ForceEnglish middleware executed', [
            'url' => $request->fullUrl(),
            'previous_locale' => App::getLocale()
        ]);

        // تعيين اللغة الإنجليزية بشكل إجباري في كل مكان
        App::setLocale('en');
        Config::set('app.locale', 'en');
        Session::put('locale', 'en');
        Session::save();

        // تفعيل متغيرات العرض للغة الإنجليزية
        view()->share('current_locale', 'en');
        view()->share('dir', 'ltr');

        // تنفيذ الطلب واستلام الاستجابة
        $response = $next($request);

        // إضافة ترويسات إلى الاستجابة لمنع التخزين المؤقت وتحديد اللغة
        if (method_exists($response, 'header')) {
            try {
                $response->header('Content-Language', 'en');
                $response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
                $response->header('Pragma', 'no-cache');
            } catch (\Exception $e) {
                Log::error('Error adding headers in ForceEnglish middleware: ' . $e->getMessage());
            }
        }

        // تسجيل نهاية العملية
        Log::info('ForceEnglish middleware completed', [
            'final_locale' => App::getLocale(),
            'session_locale' => Session::get('locale')
        ]);

        return $response;
    }
}
