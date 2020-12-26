<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;

class PullService
{
    public function pullFromRemote()
    {
        $domain = config('translationcaptain.url');
        $projectId = config('translationcaptain.projectId');
        $url = $domain. "/projects/$projectId/labels/download";

        $locales = implode(",", config('translationcaptain.locales'));
        $url .= "?locales=$locales";

        $response = Http::get($url);
        $pulledKeys = $response->json();

        $this->generateFiles($pulledKeys);

        return $pulledKeys;
    }

    private function generateFiles(array $pulledKeys) : void
    {
        $bladeFilesGenerator = new GeneratorBlade($pulledKeys);
        $bladeFilesGenerator->export();

        $bladeFilesGenerator = new GeneratorVue($pulledKeys);
        $bladeFilesGenerator->export();
    }
}
