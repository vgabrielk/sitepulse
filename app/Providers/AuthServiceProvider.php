<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for multi-tenant access control
        Gate::define('access-site', function ($user, $site) {
            return $user->id === $site->client_id;
        });

        Gate::define('admin-access', function ($user) {
            return $user->email === config('app.admin_email') || 
                   $user->plan === 'enterprise';
        });
    }
}
