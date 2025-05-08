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
use App\Models\Setting;

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
        // الإنجليزية هي اللغة الافتراضية للنظام
        $locale = 'en';
        
        // تحقق من وجود معلمة اللغة في المسار (في حالة استخدام prefix)
        if ($request->route('locale') && in_array($request->route('locale'), ['en', 'ar'])) {
            $locale = $request->route('locale');
        }
        // التحقق من وجود اللغة في الجلسة إذا لم تكن موجودة في المسار
        elseif (session('locale') && in_array(session('locale'), ['en', 'ar'])) {
            $locale = session('locale');
        }
        
        // حفظ اللغة في الجلسة
        session(['locale' => $locale]);
        
        // تعيين لغة التطبيق
        App::setLocale($locale);
        Config::set('app.locale', $locale);
        
        // Set view variables
        view()->share('current_locale', $locale);
        view()->share('dir', $locale == 'ar' ? 'rtl' : 'ltr');
        
        return $next($request);
    }
} 