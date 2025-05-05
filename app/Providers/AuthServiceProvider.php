<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // سوبر أدمن يتجاوز كل الصلاحيات
        Gate::before(function (User $user, $ability) {
            Log::info('Gate::before called', ['user_id' => $user->id, 'ability' => $ability]);
            return $user->isSuperAdmin() ? true : null;
        });
    }
} 