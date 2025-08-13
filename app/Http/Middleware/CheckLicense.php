<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    private LicenseService $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تجاوز التحقق لصفحات التثبيت والصيانة
        $allowedRoutes = [
            'install.*',
            'login',
            'logout',
            'password.*'
        ];

        foreach ($allowedRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // تجاوز التحقق إذا لم يتم التثبيت بعد
        if (!file_exists(storage_path('app/install.lock'))) {
            return $next($request);
        }

        // التحقق من الرخصة
        $license = $this->licenseService->validateLicense();

        if (!$license['is_valid']) {
            // إذا كانت الرخصة غير صالحة، إظهار صفحة تحذير
            return response()->view('errors.license-expired', [
                'license' => $license
            ], 403);
        }

        // إضافة معلومات الرخصة للـ view
        view()->share('systemLicense', $license);

        // إضافة تحذير إذا كانت الرخصة ستنتهي قريباً
        if ($license['is_expiring_soon'] && !$request->ajax()) {
            session()->flash('license_warning', [
                'message' => $license['message'],
                'days_left' => $license['days_until_expiry']
            ]);
        }

        return $next($request);
    }
}
