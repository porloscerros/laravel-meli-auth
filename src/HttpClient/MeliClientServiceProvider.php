<?php


namespace Porloscerros\Meli\HttpClient;


use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\PendingRequest;
use Porloscerros\Meli\HttpClient\Middleware\RefreshTokenMiddleware;

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
            $config = [],
            ?RefreshTokenInterface $refresher = null,
            ?RefreshTokenDeciderInterface $decider = null,
            ?RetryInterface $retryier = null
        ) {
            /** @var PendingRequest $this */
            return $this
                ->withToken($context['access_token'])
                ->accept('application/json')
                ->withMiddleware(
                    (new RefreshTokenMiddleware(
                        $refresher ?? app(RefreshTokenInterface::class),
                        $decider ?? app(RefreshTokenDeciderInterface::class),
                        $retryier ?? app(RetryInterface::class)
                    ))->__invoke($context, $config)
                );
        });
    }

    public function register()
    {
        $this->app->bind(RefreshTokenInterface::class, function ($app) {
            return $app->make(\Porloscerros\Meli\HttpClient\RefreshToken::class);
        });
        $this->app->bind(RefreshTokenDeciderInterface::class, function ($app) {
            return $app->make(\Porloscerros\Meli\HttpClient\RefreshTokenDecider::class);
        });
        $this->app->bind(RetryInterface::class, function ($app) {
            return $app->make(\Porloscerros\Meli\HttpClient\Retry::class);
        });
    }
}