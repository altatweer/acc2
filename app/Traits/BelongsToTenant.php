<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * تشغيل Boot method للتأكد من تعيين tenant_id تلقائياً
     * وتطبيق global scope لتقييد الاستعلامات حسب tenant_id
     */
    public static function bootBelongsToTenant()
    {
        // إضافة Global Scope لتقييد الاستعلامات بـ tenant_id
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (app()->bound('tenant_id') && config('app.multi_tenancy_enabled', false)) {
                $builder->where('tenant_id', app()->make('tenant_id'));
            }
        });

        // تعيين tenant_id عند إنشاء نموذج جديد
        static::creating(function ($model) {
            if (app()->bound('tenant_id') && config('app.multi_tenancy_enabled', false) && empty($model->tenant_id)) {
                $model->tenant_id = app()->make('tenant_id');
            }
        });
    }

    /**
     * تحديد العلاقة مع Tenant
     * سيتم تفعيل هذه العلاقة لاحقاً عند ربط النظام بمنصة SaaS
     */
    public function tenant()
    {
        // هذه العلاقة ستتم إضافتها لاحقاً عند تنفيذ نظام متعدد المستأجرين
        // حالياً، تركناها كتعليق لتذكيرنا بتنفيذها عند الحاجة
        
        // return $this->belongsTo(Tenant::class);
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