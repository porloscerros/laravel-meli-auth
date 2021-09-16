<?php

namespace Porloscerros\Meli\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Porloscerros\Meli\Events\TokenGotten;
//use Porloscerros\Meli\Listeners\SaveCustomerToken;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        TokenGotten::class => [
            //SaveCustomerToken::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
} 
