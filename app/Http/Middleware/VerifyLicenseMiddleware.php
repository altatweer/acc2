<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VerifyLicenseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $purchaseFile = storage_path('app/private/purchase.json');
        if (!file_exists($purchaseFile)) {
            abort(403, 'License file missing. Please reinstall or contact support.');
        }
        $data = json_decode(file_get_contents($purchaseFile), true);
        $purchase_code = $data['purchase_code'] ?? '';
        $domain = $data['domain'] ?? '';
        $last_check = $data['last_check'] ?? null;

        // تحقق كل 7 أيام فقط (يمكنك تغيير المدة)
        if (!$last_check || Carbon::parse($last_check)->diffInDays(now()) >= 7) {
            $verifyUrl = 'https://envatocode.aursuite.com/envato-verify.php?purchase_code=' . urlencode($purchase_code) . '&domain=' . urlencode($domain);
            $ch = curl_init($verifyUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $verify = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($verify, true);

            if (empty($result['success'])) {
                abort(403, $result['message'] ?? 'License verification failed. Please contact support.');
            }
            // حدث وقت آخر تحقق
            $data['last_check'] = now()->toDateTimeString();
            file_put_contents($purchaseFile, json_encode($data));
        }

        return $next($request);
    }
} 