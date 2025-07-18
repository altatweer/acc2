<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureTenantId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // دائماً نضع tenant_id = 1 كقيمة افتراضية
            $tenantId = 1;
            
            // إذا كان المستخدم مسجل الدخول
            if (Auth::check()) {
                $user = Auth::user();
                
                // إذا لم يكن لديه tenant_id أو كان NULL، نحدثه
                if (!$user->tenant_id || is_null($user->tenant_id)) {
                    $user->tenant_id = 1;
                    $user->save();
                    Log::info("تم تحديث tenant_id للمستخدم {$user->email} إلى 1");
                }
                
                $tenantId = $user->tenant_id;
            }
            
            // تعيين tenant_id في الـ container وال session
            app()->instance('tenant_id', $tenantId);
            session(['tenant_id' => $tenantId]);
            
        } catch (\Exception $e) {
            Log::error('EnsureTenantId middleware error: ' . $e->getMessage());
            // في حالة الخطأ، نضع القيمة الافتراضية
            app()->instance('tenant_id', 1);
            session(['tenant_id' => 1]);
        }
        
        return $next($request);
    }
} 