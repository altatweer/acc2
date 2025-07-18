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
            $tenantId = 1; // القيمة الافتراضية

            // إذا كانت خاصية تعدد المستأجرين مفعلة
            if (config('app.multi_tenancy_enabled', false)) {
                // محاولة الحصول على tenant_id من المستخدم المسجل
                if (auth()->check() && auth()->user()) {
                    $user = auth()->user();
                    
                    // إذا كان المستخدم ليس لديه tenant_id أو كان NULL، نحدثه
                    if (!isset($user->tenant_id) || is_null($user->tenant_id)) {
                        $user->update(['tenant_id' => 1]);
                        $tenantId = 1;
                    } else {
                        $tenantId = $user->tenant_id;
                    }
                }
                // إذا كان هناك tenant_id في الجلسة، نستخدمه
                elseif (session()->has('tenant_id')) {
                    $tenantId = session('tenant_id');
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