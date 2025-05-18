<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'billing_cycle',
        'trial_days',
        'features',
        'limits',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'trial_days' => 'integer',
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenants that belong to this subscription plan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'subscription_plan_id');
    }

    /**
     * Check if a specific feature is included in this plan.
     *
     * @param string $featureCode
     * @return bool
     */
    public function hasFeature(string $featureCode): bool
    {
        return isset($this->features[$featureCode]) && $this->features[$featureCode];
    }

    /**
     * Get the limit for a specific resource.
     *
     * @param string $resourceType
     * @return int
     */
    public function getLimit(string $resourceType): int
    {
        return $this->limits[$resourceType] ?? 0;
    }

    /**
     * Check if the plan has unlimited resources for a specific type.
     *
     * @param string $resourceType
     * @return bool
     */
    public function hasUnlimitedResource(string $resourceType): bool
    {
        return ($this->limits[$resourceType] ?? 0) < 0;
    }
} 