<?php

namespace Yormy\TranslationcaptainLaravel\Commands\Traits;

use Yormy\TranslationcaptainLaravel\Services\PushService;

trait PushTrait
{
    public function goPush()
    {
        $this->comment('Pushing keys and translations to TranslationCaptain');
        $this->comment('Pushing...');

        $push = new PushService(config('translationcaptain.locales'));
        $response = $push->pushToRemote();

        $body = json_decode($response->body(), true);
        $processedKeys = $body['data']['successful_processed_keys'];

        $option = $this->argument('option');
        if ($option === 'details') {
            foreach ($processedKeys as $key) {
                $this->comment('   ' . $key);
            }
        }

        $this->comment("");
        if ($response->failed()) {
            $this->comment('Captain, we pushed '. count($processedKeys). ' keys');
            $unprocessedKeyCount = $body['data']['unprocessed_keys'];
            $this->comment('we could not process '. $unprocessedKeyCount. ' keys');
            $this->comment('Arrr...'. $body['message']);
            return;
        }

        $this->comment('Captain, we pushed '. count($processedKeys). ' keys');
        $this->comment('Ahoy Captain.. we\'re done');
    }

}
