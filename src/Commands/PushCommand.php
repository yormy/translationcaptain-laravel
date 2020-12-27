<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Commands\Traits\PushTrait;

class PushCommand extends Command
{
    use PushTrait;

    public $signature = 'translationcaptain:push {option?}';

    public $description = 'Push local changes and found keys to TranslationCaptain
    "translationcaptain:push details" : print out the processed keys';

    public function handle()
    {
        $this->goPush();
    }
}
