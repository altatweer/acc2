<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'subdomain',
        'domain',
        'database',
        'logo',
        'contact_email',
        'contact_phone',
        'address',
        'is_active',
        'subscription_plan_id',
        'subscription_starts_at',
        'subscription_ends_at',
        'trial_ends_at',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the users that belong to the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the roles that belong to the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get the accounts that belong to the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Check if the tenant subscription is active.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // إذا كان تاريخ انتهاء الاشتراك غير محدد أو لا يزال في المستقبل
        return $this->subscription_ends_at === null || 
               $this->subscription_ends_at->isFuture();
    }

    /**
     * Check if the tenant is in trial period.
     *
     * @return bool
     */
    public function isOnTrial(): bool
    {
        return $this->trial_ends_at !== null && 
               $this->trial_ends_at->isFuture();
    }
} 