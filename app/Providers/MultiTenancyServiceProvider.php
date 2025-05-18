<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;

class MultiTenancyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // تسجيل singleton لـ tenant_id للاستخدام عبر التطبيق
        $this->app->singleton('tenant_id', function ($app) {
            try {
                // القيمة الافتراضية
                $tenantId = 1;
                
                // قراءة القيمة من الجلسة
                if (session()->has('tenant_id')) {
                    $tenantId = session('tenant_id');
                }
                // محاولة القراءة من المستخدم المسجل
                elseif (Auth::check() && Auth::user()) {
                    $user = Auth::user();
                    if (isset($user->tenant_id) && !is_null($user->tenant_id)) {
                        $tenantId = $user->tenant_id;
                    }
                }
                
                return $tenantId;
            } catch (\Exception $e) {
                Log::error('MultiTenancyServiceProvider singleton error: ' . $e->getMessage());
                return 1; // القيمة الافتراضية في حالة حدوث خطأ
            }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        try {
            // تحديد إعداد التفعيل من ملف .env
            $multiTenancyEnabled = env('MULTI_TENANCY_ENABLED', false);
            Config::set('app.multi_tenancy_enabled', $multiTenancyEnabled);
            
            // إذا لم يكن مفعلاً، نتوقف هنا
            if (!$multiTenancyEnabled) {
                return;
            }
            
            // ضمان تعيين tenant_id تلقائياً لكل Model جديد
            Model::creating(function ($model) {
                if (config('app.multi_tenancy_enabled', false) && 
                    method_exists($model, 'getConnection') && 
                    $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'tenant_id') &&
                    !isset($model->tenant_id)) {
                    $model->tenant_id = app()->bound('tenant_id') ? app('tenant_id') : 1;
                }
            });

            // استمع لحدث تسجيل الدخول لتعيين tenant_id
            Event::listen(Login::class, function (Login $event) {
                try {
                    $user = $event->user;
                    
                    if ($user && isset($user->tenant_id) && !is_null($user->tenant_id)) {
                        $tenantId = $user->tenant_id;
                        session(['tenant_id' => $tenantId]);
                        $this->app->instance('tenant_id', $tenantId);
                    }
                } catch (\Exception $e) {
                    Log::error('MultiTenancyServiceProvider login event error: ' . $e->getMessage());
                }
            });
            
            // إضافة تحقق للصلاحيات على مستوى المستأجر
            Gate::before(function ($user, $ability) {
                // إذا لم يكن نظام متعدد المستأجرين مفعل، نتخطى التحقق
                if (!config('app.multi_tenancy_enabled', false)) {
                    return null;
                }
                
                // السماح للمستخدمين المشرفين بالوصول إلى جميع البيانات بغض النظر عن tenant_id
                if ($user->isSuperAdmin() || (method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
                    return null;
                }
                
                // التحقق من تطابق tenant_id للمستخدم مع tenant_id الحالي
                $currentTenantId = app()->bound('tenant_id') ? app('tenant_id') : 1;
                if ($user->tenant_id !== $currentTenantId) {
                    return false; // منع الوصول إذا كان المستخدم من مستأجر مختلف
                }
                
                return null; // استمرار في سلسلة تحقق الصلاحيات
            });
        } catch (\Exception $e) {
            Log::error('MultiTenancyServiceProvider boot error: ' . $e->getMessage());
        }
    }
} 