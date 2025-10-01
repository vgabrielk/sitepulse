<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);
        
        // Register API key authentication
        $this->app['auth']->extend('api', function ($app, $name, $config) {
            $provider = new \App\Auth\ApiUserProvider();
            return new \App\Auth\ApiGuard($provider, $app['request']);
        });
    }
}