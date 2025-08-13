<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    private const CACHE_KEY = 'system_license';
    private const CACHE_DURATION = 3600; // ساعة واحدة

    /**
     * التحقق من صحة الرخصة
     */
    public function validateLicense(): array
    {
        // التحقق من الكاش أولاً
        $cached = Cache::get(self::CACHE_KEY);
        if ($cached && $cached['expires_at'] > now()) {
            return $cached;
        }

        // إذا كان في بيئة التطوير، استخدم رخصة تطوير
        if (app()->environment('local', 'development')) {
            return $this->getDevelopmentLicense();
        }

        // البحث عن رخصة نشطة
        $domain = request()->getHost();
        $license = License::findActiveForDomain($domain);

        if (!$license) {
            // إنشاء رخصة تطوير افتراضية إذا لم توجد
            $license = License::createDevelopmentLicense($domain);
            Log::info('تم إنشاء رخصة تطوير افتراضية', ['domain' => $domain]);
        }

        $result = $this->prepareLicenseData($license);
        
        // حفظ في الكاش
        Cache::put(self::CACHE_KEY, $result, self::CACHE_DURATION);
        
        // تحديث آخر فحص
        $license->updateLastCheck();

        return $result;
    }

    /**
     * الحصول على رخصة التطوير
     */
    private function getDevelopmentLicense(): array
    {
        return [
            'status' => 'active',
            'type' => 'development',
            'expires_at' => now()->addYear(),
            'features' => [
                'multi_currency' => true,
                'reports_export' => true,
                'api_access' => true,
                'unlimited_users' => true,
                'all_features' => true
            ],
            'limits' => [
                'max_users' => 999,
                'max_companies' => 10,
                'max_transactions' => null
            ],
            'is_valid' => true,
            'is_development' => true,
            'message' => 'رخصة تطوير نشطة'
        ];
    }

    /**
     * تحضير بيانات الرخصة
     */
    private function prepareLicenseData(License $license): array
    {
        $isValid = $license->isValid();
        
        return [
            'license_key' => $license->license_key,
            'status' => $license->status,
            'type' => $license->type,
            'expires_at' => $license->expires_at,
            'features' => $license->features ?: [],
            'limits' => $license->limits ?: [],
            'is_valid' => $isValid,
            'is_development' => $license->type === 'development',
            'is_expiring_soon' => $license->isExpiringSoon(),
            'days_until_expiry' => $license->expires_at ? $license->expires_at->diffInDays(now()) : null,
            'message' => $this->getLicenseMessage($license, $isValid)
        ];
    }

    /**
     * الحصول على رسالة حالة الرخصة
     */
    private function getLicenseMessage(License $license, bool $isValid): string
    {
        if (!$isValid) {
            if ($license->status !== 'active') {
                return "الرخصة غير نشطة: {$license->status}";
            }
            if ($license->expires_at && $license->expires_at->isPast()) {
                return 'انتهت صلاحية الرخصة في ' . $license->expires_at->format('Y-m-d');
            }
            return 'الرخصة غير صحيحة';
        }

        if ($license->type === 'development') {
            if (!$license->expires_at) {
                return "رخصة تطوير نشطة (بلا حدود زمنية)";
            }
            $daysLeft = $license->expires_at->isFuture() ? 
                $license->expires_at->diffInDays(now()) : 
                0;
            return "رخصة تطوير نشطة (متبقي: {$daysLeft} يوم)";
        }

        if ($license->isExpiringSoon()) {
            $daysLeft = $license->expires_at->diffInDays(now());
            return "الرخصة صالحة (تنتهي خلال {$daysLeft} يوم)";
        }

        return 'الرخصة صالحة ونشطة';
    }

    /**
     * التحقق من ميزة معينة
     */
    public function hasFeature(string $feature): bool
    {
        $license = $this->validateLicense();
        
        if (!$license['is_valid']) {
            return false;
        }

        // رخص التطوير لها جميع الميزات
        if ($license['is_development']) {
            return true;
        }

        return in_array($feature, $license['features']) || 
               (isset($license['features'][$feature]) && $license['features'][$feature] === true);
    }

    /**
     * الحصول على حد معين
     */
    public function getLimit(string $limit, $default = null)
    {
        $license = $this->validateLicense();
        
        if (!$license['is_valid']) {
            return $default;
        }

        return $license['limits'][$limit] ?? $default;
    }

    /**
     * التحقق من صحة مفتاح الرخصة
     */
    public function validateLicenseKey(string $key): array
    {
        // تنسيق مفاتيح التطوير (مرن للنص والأرقام)
        if (preg_match('/^DEV-\d{4}-[A-Z0-9]{4,}$/i', $key)) {
            return [
                'valid' => true,
                'type' => 'development',
                'message' => 'مفتاح رخصة تطوير صحيح'
            ];
        }

        // تنسيق مفاتيح الإنتاج (للمستقبل)
        if (preg_match('/^PROD-\d{4}-[A-Z0-9]{12}$/i', $key)) {
            return [
                'valid' => true,
                'type' => 'production',
                'message' => 'مفتاح رخصة إنتاج صحيح'
            ];
        }

        return [
            'valid' => false,
            'type' => 'unknown',
            'message' => 'مفتاح رخصة غير صحيح'
        ];
    }

    /**
     * تنشيط رخصة جديدة
     */
    public function activateLicense(string $key, string $domain = null): array
    {
        $validation = $this->validateLicenseKey($key);
        
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $domain = $domain ?: request()->getHost();

        // البحث عن الرخصة
        $license = License::findByKey($key);
        
        if (!$license) {
            // إنشاء رخصة جديدة للتطوير
            if ($validation['type'] === 'development') {
                $license = License::create([
                    'license_key' => $key,
                    'type' => 'development',
                    'status' => 'active',
                    'domain' => $domain,
                    'expires_at' => now()->addYear(),
                    'features' => [
                        'multi_currency' => true,
                        'reports_export' => true,
                        'api_access' => true,
                        'unlimited_users' => true,
                        'all_features' => true
                    ],
                    'limits' => [
                        'max_users' => 999,
                        'max_companies' => 10,
                        'max_transactions' => null
                    ],
                    'notes' => 'رخصة تطوير مفعلة من التثبيت'
                ]);
            } else {
                return [
                    'success' => false,
                    'message' => 'مفتاح الرخصة غير موجود في النظام'
                ];
            }
        }

        // تحديث النطاق إذا كان مختلفاً
        if ($license->domain !== $domain) {
            $license->update(['domain' => $domain]);
        }

        // مسح الكاش
        Cache::forget(self::CACHE_KEY);

        return [
            'success' => true,
            'message' => 'تم تنشيط الرخصة بنجاح',
            'license' => $this->prepareLicenseData($license)
        ];
    }

    /**
     * الحصول على معلومات الرخصة الحالية
     */
    public function getCurrentLicense(): array
    {
        return $this->validateLicense();
    }

    /**
     * مسح كاش الرخصة
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
