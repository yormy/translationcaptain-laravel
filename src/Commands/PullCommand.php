<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Commands\Traits\PullTrait;

class PullCommand extends Command
{
    use PullTrait;

    public $signature = 'translationcaptain:pull {option?}';

    public $description = 'Pull translations from TranslationCaptain to refresh local files';

    public function handle()
    {
        $this->goPull();
    }
}
