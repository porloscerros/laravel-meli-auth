<?php


namespace Porloscerros\Meli\Providers;


use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\PendingRequest;

class MeliClientServiceProvider extends ServiceProvider
{


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        PendingRequest::macro('meliClient', function (
            $context = [],
            $config = []
        ) {
            /** @var PendingRequest $this */
            return $this
                ->withToken($context['access_token'])
                ->accept('application/json');
        });
    }

    public function register()
    {

    }
}