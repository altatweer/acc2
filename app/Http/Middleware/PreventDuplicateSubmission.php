<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تطبيق فقط على طلبات POST, PUT, PATCH
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $token = $request->input('_token');
            
            // تحقق مما إذا كانت الجلسة تحتوي على هذا الطلب
            $sessionKey = 'submitted_' . $token;
            
            if (Session::has($sessionKey)) {
                // طلب مكرر، قم بإعادة توجيه المستخدم بدون معالجة الطلب
                return redirect()->back()->with('error', __('messages.duplicate_submission'));
            }
            
            // قم بتخزين الطلب في الجلسة
            Session::put($sessionKey, true);
            Session::save();
            
            // قم بمعالجة الاستجابة
            $response = $next($request);
            
            // قم بإزالة الطلب من الجلسة
            Session::forget($sessionKey);
            Session::save();
            
            return $response;
        }
        
        // لطلبات أخرى، استمر بالمعالجة العادية
        return $next($request);
    }
} 