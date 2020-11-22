<?php

namespace Yormy\TranslationcaptainLaravel\Commands;

use Illuminate\Console\Command;
use Yormy\TranslationcaptainLaravel\Services\ImportLaravel;

class TranslationcaptainLaravelCommand extends Command
{
    public $signature = 'tcp';

    public $description = 'My command';

    public function handle()
    {
        echo "kkkk";

        $importer = new ImportLaravel();
        $messages = $importer->getMessages();

        dd($messages);


        $this->comment('All done');
    }
}
