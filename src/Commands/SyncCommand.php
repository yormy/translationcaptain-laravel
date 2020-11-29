<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Services\ImportLaravel;
use Yormy\TranslationcaptainLaravel\Services\PullService;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class SyncCommand extends Command
{
    public $signature = 'tcp:sync';

    public $description = 'Push changes to TranslationCaptain and Pull to refresh local files';

    public function handle()
    {
        $this->comment('TranslationCaptain Syncing, First Push Then Pull ');

        $this->comment('Pushing...');
        $locales = ['nl','en'];
        $push = new PushService($locales);
        $push->pushToRemote();

        $this->comment('Ahoy, let start Pulling...');
        $push = new PullService();
        $push->pullFromRemote();

        $this->comment('Ahoy Captain.. we\'re done');
    }
}
