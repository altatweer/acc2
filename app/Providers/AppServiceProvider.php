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
use Illuminate\Database\Eloquent\Model;
use App\Services\LanguageService;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // زيادة حد الذاكرة إذا كان أقل من 512 ميجابايت
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitInBytes = $this->getMemoryLimitInBytes($memoryLimit);
        if ($memoryLimitInBytes < 536870912) { // 512MB in bytes
            ini_set('memory_limit', '512M');
        }
    }

    /**
     * تحويل حد الذاكرة إلى بايت
     */
    private function getMemoryLimitInBytes($memoryLimit)
    {
        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) substr($memoryLimit, 0, -1);
        
        switch ($unit) {
            case 'g':
                return $value * 1024 * 1024 * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'k':
                return $value * 1024;
            default:
                return (int) $memoryLimit;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // إضافة tenant_id افتراضي عند تفعيل نظام تعدد المستأجرين
        if (config('app.multi_tenancy_enabled', false)) {
            app()->instance('tenant_id', 1);
        }
        
        // تعطيل تتبع التغييرات في Eloquent للتحسين
        Model::preventLazyLoading(!app()->isProduction());
        
        // تجنب التشغيل الكامل عند تنفيذ الأوامر من CLI لتوفير الذاكرة
        if ($this->app->runningInConsole()) {
            return;
        }
        
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
            // تفعيل اللغة من الإعدادات فقط
            $defaultLang = null;
            try {
                $defaultLang = Setting::get('default_language', config('app.locale'));
            } catch (\Throwable $e) {
                $defaultLang = config('app.locale');
            }
            App::setLocale($defaultLang);
            Config::set('app.locale', $defaultLang);
            
            // تحسين أداء استعلامات قاعدة البيانات
            \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());
            
            // إضافة مراقبة الاستعلامات الثقيلة في بيئة التطوير
            if (app()->isLocal()) {
                \DB::listen(function ($query) {
                    if ($query->time > 100) {  // أكثر من 100 مللي ثانية
                        \Log::warning('Slow DB Query: ' . $query->sql, [
                            'time' => $query->time,
                            'bindings' => $query->bindings,
                        ]);
                    }
                });
            }
            
            // استخدام التخزين المؤقت للبيانات التي يتم استعلامها بشكل متكرر
            try {
                \Cache::remember('currencies', 60*24, function () {
                    return \App\Models\Currency::all();
                });
            } catch (\Exception $e) {
                // Silently handle any database connection errors
                \Log::error('Failed to cache currencies: ' . $e->getMessage());
            }
        } else {
            // During installer, use config/app locale only
            $defaultLang = config('app.locale');
            App::setLocale($defaultLang);
            Config::set('app.locale', $defaultLang);
        }
    }
}
