<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key',
        'type',
        'status',
        'domain',
        'expires_at',
        'last_check',
        'features',
        'limits',
        'notes'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_check' => 'datetime',
        'features' => 'array',
        'limits' => 'array',
    ];

    /**
     * التحقق من صحة الرخصة
     */
    public function isValid(): bool
    {
        // التحقق من الحالة
        if ($this->status !== 'active') {
            return false;
        }

        // التحقق من انتهاء الصلاحية
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * التحقق من انتهاء الصلاحية قريباً
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        return $this->expires_at->diffInDays(now()) <= $days && $this->expires_at->isFuture();
    }

    /**
     * الحصول على الميزات المتاحة
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->features) {
            return true; // للتطوير، جميع الميزات متاحة
        }

        return in_array($feature, $this->features) || 
               (isset($this->features[$feature]) && $this->features[$feature] === true);
    }

    /**
     * الحصول على حد معين
     */
    public function getLimit(string $limit, $default = null)
    {
        if (!$this->limits) {
            return $default;
        }

        return $this->limits[$limit] ?? $default;
    }

    /**
     * تحديث آخر فحص
     */
    public function updateLastCheck(): void
    {
        $this->update(['last_check' => now()]);
    }

    /**
     * إنشاء رخصة تطوير افتراضية
     */
    public static function createDevelopmentLicense(string $domain = null): self
    {
        return self::create([
            'license_key' => 'DEV-2025-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'type' => 'development',
            'status' => 'active',
            'domain' => $domain,
            'expires_at' => now()->addYear(), // صالح لسنة
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
                'max_transactions' => null // بلا حدود
            ],
            'notes' => 'رخصة تطوير تلقائية - صالحة لسنة واحدة'
        ]);
    }

    /**
     * البحث عن رخصة حسب المفتاح
     */
    public static function findByKey(string $key): ?self
    {
        return self::where('license_key', $key)->first();
    }

    /**
     * البحث عن رخصة نشطة لدومين معين
     */
    public static function findActiveForDomain(string $domain): ?self
    {
        return self::where('domain', $domain)
                   ->where('status', 'active')
                   ->where(function($query) {
                       $query->whereNull('expires_at')
                             ->orWhere('expires_at', '>', now());
                   })
                   ->first();
    }
}
