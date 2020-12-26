<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class PushCommand extends Command
{
    public $signature = 'tcp:push';

    public $description = 'Push local changes and found keys to TranslationCaptain';

    public function handle()
    {
        $this->comment('Pushing keys and translations to TranslationCaptain');
        $this->comment('Pushing...');

        $push = new PushService(config('translationcaptain-laravel.locales'));
        $push->pushToRemote();

        $this->comment('Ahoy Captain.. we\'re done');
    }
}
