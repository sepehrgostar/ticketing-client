<?php

namespace Sepehrgostar\Ticketing;

use Illuminate\Support\ServiceProvider;

class TicketingClientServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'TicketingClient');
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
        $this->mergeConfigFrom(__DIR__ . '/config/TicketingClient.php', 'TicketingClient');

        // Register the service the package provides.
        $this->app->singleton('TicketingClient', function ($app) {
            return new TicketingClient;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['TicketingClient'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {

        //php artisan vendor:publish --tag=sepehrgostar.ticketingClient.views

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/config/TicketingClient.php' => config_path('TicketingClient.php'),
        ], 'sepehrgostar.ticketingClient.config');


        // Publishing the views.
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/ticketing')
        ], 'sepehrgostar.ticketingClient.views');


        // Publishing assets.
        $this->publishes([
            __DIR__ . '/resources/assets' => public_path('vendor/sepehrgostar/ticketingClient'),
        ], 'sepehrgostar.ticketingClient.views');


    }
}
