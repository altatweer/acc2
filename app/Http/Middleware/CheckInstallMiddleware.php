<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInstallMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $lockPath = storage_path('app/install.lock');
        $isInstaller = $request->is('install*') || $request->is('installer_check.php');
        if (!file_exists($lockPath) && !$isInstaller) {
            return redirect('/install')->with('install_notice', 'يجب تثبيت النظام أولاً قبل الاستخدام.');
        }
        return $next($request);
    }
} 