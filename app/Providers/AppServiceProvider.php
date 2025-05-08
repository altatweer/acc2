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
        
        // تهيئة الطول الأقصى للسلاسل النصية للإصدارات القديمة من MySQL
        Schema::defaultStringLength(191);
        
        // في حالة HTTPS
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        
        // Add macro for localized route generation without language prefix
        Route::macro('localizedRoute', function ($name, $parameters = [], $absolute = true) {
            // Do not modify parameters - no locale needed
            return route($name, $parameters, $absolute);
        });
        
        // Extend the redirector to handle routes without language prefix
        $this->app->extend('redirect', function (Redirector $redirector, $app) {
            $redirector->macro('localizedRoute', function ($route, $parameters = [], $status = 302, $headers = []) {
                // Do not add locale parameter
                return $this->route($route, $parameters, $status, $headers);
            });
            
            return $redirector;
        });
    }
}
