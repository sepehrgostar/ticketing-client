<?php

namespace Sepehrgostar\Ticketing;

use Illuminate\Support\ServiceProvider;

class TicketingServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Ticketing');
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
        $this->mergeConfigFrom(__DIR__ . '/config/ticketing.php', 'ticketing');

        // Register the service the package provides.
        $this->app->singleton('ticketing', function ($app) {
            return new Ticketing;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Ticketing'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/config/ticketing.php' => config_path('ticketing.php'),
        ], 'sepehrgostar.ticketing.config');


        //php artisan vendor:publish --tag=sepehrgostar.Ticketing.views
        // Publishing the views.

        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/ticketing')
        ], 'sepehrgostar.ticketing.views');


        // Publishing assets.
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('vendor/sepehrgostar'),
        ], 'sepehrgostar.ticketing.views');


    }
}
