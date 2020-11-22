<?php

namespace Yormy\TranslationcaptainLaravel\Observers;

use Illuminate\Events\Dispatcher;
use Yormy\TranslationcaptainLaravel\Observers\Events\AwardReferrerEvent;
use Yormy\TranslationcaptainLaravel\Observers\Events\AwardRevokeEvent;
use Yormy\TranslationcaptainLaravel\Observers\Listeners\AwardReferrerListener;
use Yormy\TranslationcaptainLaravel\Observers\Listeners\AwardRevokeListener;

class ActionSubscriber
{
    /**
     * Binding of SettingsChanged Events
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            AwardReferrerEvent::class,
            AwardReferrerListener::class
        );

        $events->listen(
            AwardRevokeEvent::class,
            AwardRevokeListener::class
        );
    }
}
