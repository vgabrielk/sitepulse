<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use App\Helpers\PermissionHelper;

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

        // Register Blade directives for permissions
        Blade::directive('can', function ($permission) {
            return "<?php if (App\Helpers\PermissionHelper::can($permission)): ?>";
        });

        Blade::directive('endcan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canany', function ($permissions) {
            return "<?php if (App\Helpers\PermissionHelper::canAny($permissions)): ?>";
        });

        Blade::directive('endcanany', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hasrole', function ($role) {
            return "<?php if (App\Helpers\PermissionHelper::hasRole($role)): ?>";
        });

        Blade::directive('endhasrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('isadmin', function () {
            return "<?php if (App\Helpers\PermissionHelper::isAdmin()): ?>";
        });

        Blade::directive('endisadmin', function () {
            return "<?php endif; ?>";
        });
    }
}