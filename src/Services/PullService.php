<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\FilesGenerator;

class PullService
{
    private $generators;

    public function __construct()
    {
        foreach (config('translationcaptain.writers') as $writerConfig) {
            $writer = new $writerConfig['class']();
            $writer->setExportPath(base_path(). $writerConfig['path']);
            $this->addWriter($writer);
        }
    }

    public function addWriter(FilesGenerator $writer)
    {
        $this->generators[] = $writer;
    }

    public function pullFromRemote()
    {
        $domain = config('translationcaptain.url');
        $projectId = config('translationcaptain.project_id');
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
        foreach ($this->generators as $generator) {
            $generator->setLabels($pulledKeys);
            $generator->export();
        }
    }
}
