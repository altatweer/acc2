<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetTenantId
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // إذا كانت خاصية تعدد المستأجرين غير مفعلة، نتجاهل هذا الميدلوير
            if (!config('app.multi_tenancy_enabled', false)) {
                return $next($request);
            }

            // تعيين tenant_id من المستخدم الحالي إذا كان موجوداً
            $tenantId = 1; // القيمة الافتراضية

            // تعيين tenant_id من جلسة المستخدم إذا كانت موجودة
            if (session()->has('tenant_id')) {
                $tenantId = session('tenant_id');
            }
            // محاولة الحصول على tenant_id من المستخدم المسجل
            elseif (auth()->check() && auth()->user()) {
                $user = auth()->user();
                if (isset($user->tenant_id) && !is_null($user->tenant_id)) {
                    $tenantId = $user->tenant_id;
                }
            }

            // تخزين tenant_id في الكونتينر
            app()->instance('tenant_id', $tenantId);
            
            // حفظه في الجلسة للاستخدام المستقبلي
            session(['tenant_id' => $tenantId]);

            return $next($request);
        } catch (\Exception $e) {
            // تسجيل الخطأ والاستمرار دون تعديل
            Log::error('SetTenantId error: ' . $e->getMessage());
            return $next($request);
        }
    }
} 