<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;

class ViewServiceProvider extends BaseViewServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();
        
        // Override the view finder registration
        $this->app->bind('view.finder', function ($app) {
            $paths = $app['config']['view.paths'] ?? [resource_path('views')];
            return new \Illuminate\View\FileViewFinder($app['files'], $paths);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        parent::boot();
    }
}
