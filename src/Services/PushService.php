<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Yormy\TranslationcaptainLaravel\Exceptions\DuplicateKeyException;
use Yormy\TranslationcaptainLaravel\Exceptions\PushFailedException;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\FileReader;

class PushService
{
    protected $locales;

    protected $readers;

    public function __construct(array $locales)
    {
        $this->locales = $locales;

        foreach (config('translationcaptain.readers') as $readerConfig) {
            $reader = new $readerConfig['class']($locales);
            $reader->setImportPath(base_path(). $readerConfig['path']);
            $this->addReader($reader);
        }
    }

    public function addReader(FileReader $reader)
    {
        $this->readers[] = $reader;
    }

    public function pushToRemote()
    {
        $allKeys = $this->getAllKeys();

        $domain = (string)config('translationcaptain.url');
        $projectId = (string)config('translationcaptain.project_id');
        $url = $domain. "/projects/$projectId/labels/upload";

        $data = [
            'translations' => base64_encode(json_encode($allKeys)),
            'base_locale' => config('translationcaptain.default_locale'),
        ];

        try {
            $response = Http::post($url, $data);
        } catch (\Exception $e) {
            throw new PushFailedException($e->getMessage());
        }

        $this->deleteQueue();

        return $response;
    }

    private function deleteQueue()
    {
        $queueFilename = config('translationcaptain.queue_filename');
        Storage::delete($queueFilename);
    }

    public function getAllKeys()
    {
        $existingTranslations = $this->getExistingTranslations();
        $missingKeys = $this->getMissingKeys($existingTranslations);

        return $this->mergeLabels($existingTranslations, $missingKeys);
    }

    private function getExistingTranslations()
    {
        $labels = [];
        foreach ($this->readers as $reader) {
            $readLabels = $reader->getMessages();
            $labels = $this->mergeLabels($labels, $readLabels);
        }

        return $labels;
    }

    private function getMissingKeys(array $existingTranslations) : array
    {
        $importer = new SearchSources();
        $foundKeys = $importer->getMessages();

        $foundKeysDotted = Arr::dot($foundKeys);

        $missingKeys = [];
        foreach ($this->locales as $locale) {
            $missingKeysForLanguage = [];

            if (array_key_exists($locale, $existingTranslations)) {
                $existingForLanguageDotted = Arr::dot($existingTranslations[$locale]);

                foreach (array_keys($foundKeysDotted) as $key) {
                    if (! array_key_exists($key, $existingForLanguageDotted)) {
                        $missingKeysForLanguage = $this->addMissingKey($key, $missingKeysForLanguage);
                    }
                }
            } else {
                foreach (array_keys($foundKeysDotted) as $key) {
                    $missingKeysForLanguage = $this->addMissingKey($key, $missingKeysForLanguage);
                }
            }

            $missingKeys[$locale] = $missingKeysForLanguage;
        }

        return $missingKeys;
    }

    private function addMissingKey(string $fullKey, array $missingKeys) : array
    {
        $firstDot = strpos($fullKey, '.');
        if ($firstDot === false || $firstDot === 0) {
            return $missingKeys;
        }

        $group = substr($fullKey, 0, $firstDot);
        $key = substr($fullKey, $firstDot + 1, strlen($fullKey));

        $missingKeys[$group][$key] = "#$group.$key";

        return $missingKeys;
    }

    private function mergeLabels(array $origin, array $toMerge) : array
    {
        $this->checkMerge($origin, $toMerge);

        return array_replace_recursive($origin, $toMerge);
    }

    public function checkMerge(array $labels, array $labelsToMerge)
    {
        $labelsDotted = Arr::dot($labels);
        $labelsToMergeDotted = Arr::dot($labelsToMerge);

        foreach ($labelsDotted as $key => $translation) {
            if (array_key_exists($key, $labelsToMergeDotted)) {
                $labelTranslation = $this->removeBinding($translation);
                $labelTranslationToMerge = $this->removeBinding($labelsToMergeDotted[$key]);

                if ($labelTranslation !== $labelTranslationToMerge) {
                    throw new DuplicateKeyException($key, $labelTranslation, $labelTranslationToMerge);
                }
            }
        }
    }

    public function removeBinding(string $translation): string
    {
        $start = config('translationcaptain.databinding.start');
        $end = config('translationcaptain.databinding.end');
        $pattern = "$start(.*?)$end";

        return preg_replace("/". $pattern ."/", '', $translation);
    }
}
