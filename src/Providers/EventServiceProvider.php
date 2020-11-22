<?php

namespace Yormy\ReferralSystem\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Yormy\ReferralSystem\Observers\ActionSubscriber;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        ActionSubscriber::class,
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
