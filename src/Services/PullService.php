<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Http;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\FileWriter;

class PullService
{
    private $writers;

    public function __construct()
    {
        foreach (config('translationcaptain.writers') as $writerConfig) {
            $writer = new $writerConfig['class']();
            $writer->setExportPath(base_path(). $writerConfig['path']);
            $this->addWriter($writer);
        }
    }

    public function addWriter(FileWriter $writer)
    {
        $this->writers[] = $writer;
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
        foreach ($this->writers as $writer) {
            $writer->setLabels($pulledKeys);
            $writer->export();
        }
    }
}
