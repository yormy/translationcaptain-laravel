<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Http;

class PullService
{
    public function getAllKeys()
    {
        $url = 'https://backend.bedrock.local/api/v1/translationcaptain/labels/upload';
        $url = 'localhost/api/v1/translationcaptain/labels/download';
        // $url = 'https://webhook.site/cd97eddc-7bf8-4c86-8cea-4209e69da91e';

        $response = Http::get($url);

        return $response->json();
    }
}
