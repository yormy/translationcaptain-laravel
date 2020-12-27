<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Yormy\TranslationcaptainLaravel\Commands\Traits\PullTrait;
use Yormy\TranslationcaptainLaravel\Commands\Traits\PushTrait;
use Yormy\TranslationcaptainLaravel\Services\PullService;
use Yormy\TranslationcaptainLaravel\Services\PushService;

class SyncCommand extends Command
{
    use PushTrait;
    use PullTrait;

    public $signature = 'translationcaptain:sync {option?}';

    public $description = 'Push changes to TranslationCaptain and Pull to refresh local files
    "translationcaptain:sync details" : print out the processed keys';

    public function handle()
    {
        $this->goPush();

        $this->comment('   ');

        $this->goPull();
    }
}
