<?php

namespace Sepehrgostar\LaravelClient;

use Illuminate\Support\ServiceProvider;

class LaravelClientServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        //$this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sepehrgostar');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'LaravelClient');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/LaravelClient.php', 'LaravelClient');

        // Register the service the package provides.
        $this->app->singleton('LaravelClient', function ($app) {
            return new LaravelClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['LaravelClient'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/config/LaravelClient.php' => config_path('LaravelClient.php'),
        ], 'LaravelClient.config');

        // Publishing the views.
        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/sepehrgostar'),
        ], 'LaravelClient.views');

        // Publishing assets.
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('vendor/sepehrgostar'),
        ], 'sepehrgostar.LaravelClient.views');

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/sepehrgostar'),
        ], 'LaravelClient.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
