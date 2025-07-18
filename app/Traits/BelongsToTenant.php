<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

trait BelongsToTenant
{
    /**
     * تعيين bootBelongsToTenant في الموديل
     * سيتم تنفيذه عند تحميل الموديل
     * 
     * @return void
     */
    protected static function bootBelongsToTenant()
    {
        // إذا كان نظام تعدد المستأجرين غير مفعل، نتجاهل التحقق من tenant_id
        if (!config('app.multi_tenancy_enabled', false)) {
            return;
        }

        // إضافة شرط global scope للطلبات - مع تصحيح مشكلة المتغير $builder
        static::addGlobalScope('tenant_id', function (Builder $builder) {
            try {
                // فقط إذا كان عمود tenant_id موجوداً في الجدول
                $model = $builder->getModel();
                $table = $model->getTable();
                
                if (Schema::hasColumn($table, 'tenant_id')) {
                    $tenantId = app()->bound('tenant_id') ? app('tenant_id') : 1;
                    $builder->where($table . '.tenant_id', $tenantId);
                }
            } catch (\Exception $e) {
                Log::error("BelongsToTenant error: " . $e->getMessage());
            }
        });

        // حدث الإنشاء: تعيين tenant_id للسجلات الجديدة
        static::creating(function ($model) {
            try {
                // فقط إذا كان عمود tenant_id موجوداً في الجدول
                $table = $model->getTable();
                
                if (Schema::hasColumn($table, 'tenant_id')) {
                    // إذا لم يكن tenant_id محدد أو كان NULL، نعيّن 1
                    if (!isset($model->tenant_id) || is_null($model->tenant_id)) {
                        $model->tenant_id = self::getTenantId();
                    }
                    // إذا كان multi-tenancy مفعل ولكن tenant_id مختلف عن المستخدم الحالي، نصحح
                    elseif (config('app.multi_tenancy_enabled', false)) {
                        $currentTenantId = self::getTenantId();
                        if ($model->tenant_id != $currentTenantId) {
                            Log::warning("BelongsToTenant: تصحيح tenant_id من {$model->tenant_id} إلى {$currentTenantId} للموديل " . get_class($model));
                            $model->tenant_id = $currentTenantId;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("BelongsToTenant creating error: " . $e->getMessage());
                // تعيين القيمة الافتراضية إذا كان ممكناً
                if (Schema::hasColumn($model->getTable(), 'tenant_id')) {
                    $model->tenant_id = 1;
                }
            }
        });

        // حدث التحديث: التأكد من عدم تغيير tenant_id بطريقة خاطئة
        static::updating(function ($model) {
            try {
                $table = $model->getTable();
                
                if (Schema::hasColumn($table, 'tenant_id') && config('app.multi_tenancy_enabled', false)) {
                    $currentTenantId = self::getTenantId();
                    
                    // إذا كان tenant_id مختلف عن المستخدم الحالي، نمنع التحديث أو نصحح
                    if (isset($model->tenant_id) && $model->tenant_id != $currentTenantId) {
                        Log::warning("BelongsToTenant: محاولة تحديث tenant_id إلى {$model->tenant_id} بينما المستخدم الحالي له tenant_id = {$currentTenantId}");
                        $model->tenant_id = $currentTenantId;
                    }
                    
                    // إذا كان NULL، نعيّن القيمة الصحيحة
                    if (is_null($model->tenant_id)) {
                        $model->tenant_id = $currentTenantId;
                    }
                }
            } catch (\Exception $e) {
                Log::error("BelongsToTenant updating error: " . $e->getMessage());
            }
        });
    }

    /**
     * الحصول على tenant_id الحالي
     * القيمة يتم تخزينها في الذاكرة المؤقتة لتجنب استدعاء الدالة بشكل متكرر
     * 
     * @return int|null
     */
    public static function getTenantId()
    {
        // استخدام static cache لتجنب استدعاء app() مرات متعددة
        static $tenantId = null;
        
        if ($tenantId !== null) {
            return $tenantId;
        }
        
        // في الوضع التقليدي (غير متعدد المستأجرين) نستخدم 1 كـ tenant_id افتراضي
        if (!config('app.multi_tenancy_enabled', false)) {
            $tenantId = 1;
            return $tenantId;
        }

        // في حالة تفعيل النظام، نحصل على tenant_id من المستخدم الحالي
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenantId = Auth::user()->tenant_id;
            return $tenantId;
        }

        // في حالة الوصول من واجهة API أو غيرها
        $tenantId = session('tenant_id', 1);
        return $tenantId;
    }

    /**
     * العلاقة مع المستأجر
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant()
    {
        return $this->belongsTo(config('multi_tenancy.tenant_model', 'App\Models\Tenant'));
    }
    
    /**
     * تجاوز Global Scope للوصول إلى بيانات جميع المستأجرين
     * يمكن استخدامها عند الحاجة للوصول لجميع البيانات بغض النظر عن tenant_id
     */
    public static function allTenants()
    {
        return static::withoutGlobalScope('tenant');
    }
} 