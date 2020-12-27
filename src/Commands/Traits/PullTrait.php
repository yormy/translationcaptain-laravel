<?php

namespace Yormy\TranslationcaptainLaravel\Commands\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Yormy\TranslationcaptainLaravel\Services\PullService;
use Yormy\TranslationcaptainLaravel\Services\PushService;

trait PullTrait
{
    public function goPull()
    {
        $this->comment('All hands hoay, we\'re getting the translations from TranslationCaptain');
        $this->comment('Getting...');

        $pull = new PullService();
        $pulledKeys = $pull->pullFromRemote();

        $option = $this->argument('option');
        if ($option === 'details') {
            $pulledDotted = Arr::dot($pulledKeys);
            foreach ($pulledDotted as $key) {
                $this->comment('   ' . $key);
            }
        }

        $this->comment("");

        $this->comment('Captain, we pulled '. count(Arr::dot($pulledKeys)). ' keys');
        $this->comment('Ahoy Captain.. we\'re done');
    }

}
