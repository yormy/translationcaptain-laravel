<?php

namespace Yormy\TranslationcaptainLaravel\Observers;

use Illuminate\Events\Dispatcher;
use Yormy\TranslationcaptainLaravel\Observers\Events\AwardReferrerEvent;
use Yormy\TranslationcaptainLaravel\Observers\Events\AwardRevokeEvent;
use Yormy\TranslationcaptainLaravel\Observers\Events\MissingTranslationEvent;
use Yormy\TranslationcaptainLaravel\Observers\Listeners\AwardReferrerListener;
use Yormy\TranslationcaptainLaravel\Observers\Listeners\AwardRevokeListener;
use Yormy\TranslationcaptainLaravel\Observers\Listeners\MissingTranslationListener;

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
            MissingTranslationEvent::class,
            MissingTranslationListener::class
        );
    }
}
