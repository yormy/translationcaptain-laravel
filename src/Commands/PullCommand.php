<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Services\PullService;

class PullCommand extends Command
{
    public $signature = 'tcp:pull';

    public $description = 'Pull translations from TranslationCaptain to refresh local files';

    public function handle()
    {
        $this->comment('All hands hoay, we\'re getting the translations from TranslationCaptain');
        $this->comment('Getting...');

        $push = new PullService();
        $push->pullFromRemote();

        $this->comment('Ahoy Captain.. we\'re done');
    }
}
