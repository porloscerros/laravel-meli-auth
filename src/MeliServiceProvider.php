<?php

namespace Porloscerros\Meli;

use Illuminate\Support\ServiceProvider;
use Porloscerros\Meli\Providers\EventServiceProvider;
use Porloscerros\Meli\HttpClient\MeliClientServiceProvider;

class MeliServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('meli.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/meli'),
            ], 'views');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'meli');
        $this->app->singleton('meli', function () {
            return new Meli;
        });
        $this->app->register(EventServiceProvider::class);
        $this->app->register(MeliClientServiceProvider::class);
    }
}
