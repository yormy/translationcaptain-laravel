<?php

namespace Yormy\TranslationcaptainLaravel\Observers\Listeners;

use Yormy\TranslationcaptainLaravel\Observers\Events\MissingTranslationEvent;

class MissingTranslationListener
{
    public function handle(MissingTranslationEvent $event)
    {
        // Do something with the event.
    }
}
