<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Factory;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Compilers\BladeCompiler;

class CustomViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerViewFinder();
        $this->registerFactory();
        $this->registerBladeCompiler();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register the view finder implementation.
     */
    protected function registerViewFinder()
    {
        $this->app->bind('view.finder', function ($app) {
            $paths = [resource_path('views')];
            return new FileViewFinder($app['files'], $paths);
        });
    }

    /**
     * Register the view environment.
     */
    protected function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            $resolver = $app['view.engine.resolver'];
            $finder = $app['view.finder'];
            $env = new Factory($resolver, $finder, $app['events']);
            $env->setContainer($app);
            $env->share('app', $app);
            return $env;
        });
    }

    /**
     * Register the Blade compiler implementation.
     */
    protected function registerBladeCompiler()
    {
        $this->app->singleton('blade.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];
            return new BladeCompiler($app['files'], $cache);
        });
    }
}

