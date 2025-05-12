<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use App\Services\LanguageService;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // استخدام bootstrap للترقيم
        Paginator::useBootstrap();
        
        // تطبيق معامل اللغة لجميع روابط الترقيم
        Paginator::defaultView('pagination::bootstrap-4');
        
        // إضافة معلمة اللغة لجميع روابط الترقيم تلقائياً
        Paginator::queryStringResolver(function () {
            $query = request()->query();
            if (!isset($query['lang']) && App::getLocale()) {
                $query['lang'] = App::getLocale();
            }
            return $query;
        });
        
        // إضافة ماكرو جديد للفئة URL لضمان إضافة معلمة اللغة تلقائيًا
        URL::macro('withLanguage', function ($path) {
            $query = request()->query();
            $query['lang'] = App::getLocale();
            
            // إنشاء URL جديد مع معلمة اللغة
            return url($path) . '?' . http_build_query($query);
        });
        
        // إضافة توجيه جديد للروابط في قوالب Blade
        Blade::directive('langRoute', function ($expression) {
            return "<?php echo route($expression, array_merge(['lang' => app()->getLocale()], request()->query())); ?>";
        });
        
        // إضافة Blade directive جديد لدعم الـ Route::localizedRoute في قوالب Blade
        Blade::directive('localizedRoute', function ($expression) {
            return "<?php echo Route::localizedRoute($expression); ?>";
        });
        
        // إضافة توجيهية blade جديدة لاختبار اللغة الحالية
        Blade::if('arabic', function () {
            return LanguageService::isArabic();
        });
        
        Blade::if('english', function () {
            return !LanguageService::isArabic();
        });
        
        // Only run DB-dependent code if not in installer
        if (!request()->is('install*') && file_exists(base_path('.env')) && env('DB_DATABASE')) {
            Schema::defaultStringLength(191);
            if (config('app.env') !== 'local') {
                URL::forceScheme('https');
            }
            Route::macro('localizedRoute', function ($name, $parameters = [], $absolute = true) {
                return route($name, $parameters, $absolute);
            });
            $this->app->extend('redirect', function (Redirector $redirector, $app) {
                $redirector->macro('localizedRoute', function ($route, $parameters = [], $status = 302, $headers = []) {
                    return $this->route($route, $parameters, $status, $headers);
                });
                return $redirector;
            });
            // تفعيل اللغة من الجلسة أو من الإعدادات الافتراضية
            $defaultLang = null;
            try {
                $defaultLang = Setting::get('default_language', config('app.locale'));
            } catch (\Throwable $e) {
                $defaultLang = config('app.locale');
            }
            App::setLocale(
                Session::get('locale', $defaultLang)
            );
        } else {
            // During installer, use config/app locale only
            App::setLocale(
                Session::get('locale', config('app.locale'))
            );
        }
    }
}
