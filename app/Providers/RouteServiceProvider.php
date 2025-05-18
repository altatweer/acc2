<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // إضافة طريقة localizedRoute للدعم الاختصارات متعددة اللغات
        Route::macro('localizedRoute', function ($name, $parameters = [], $absolute = true) {
            return route($name, $parameters, $absolute);
        });
    }
} 