<?php declare(strict_types = 1);

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Yormy\TranslationcaptainLaravel\Exceptions\DuplicateKeyException;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderBlade;
use Yormy\TranslationcaptainLaravel\Services\FileReaders\ReaderVue;

class PushService
{
    protected $locales;

    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public function pushToRemote()
    {
        $allKeys = $this->getAllKeys();

        $domain = config('translationcaptain-laravel.url');
        $url = $domain. '/labels';
        $data = [
            'translations' => base64_encode(json_encode($allKeys)),
        ];

        $response = Http::post($url, $data);

        $this->deleteQueue();
    }

    private function deleteQueue()
    {
        $queueFilename = config('translationcaptain-laravel.queue_filename');
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
        $blade = new ReaderBlade($this->locales);
        $importPath = base_path() . config('translationcaptain-laravel.paths.blade');
        $blade->setImportPath($importPath);
        $bladeLabels = $blade->getMessages();

        $vue = new ReaderVue($this->locales);
        $importPath = base_path() . config('translationcaptain-laravel.paths.vue');
        $vue->setImportPath($importPath);
        $vueLabels = $vue->getMessages();

        return $this->mergeLabels($bladeLabels, $vueLabels);
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
        $start = config('translationcaptain-laravel.databinding.start');
        $end = config('translationcaptain-laravel.databinding.end');
        $pattern = "$start(.*?)$end";

        return preg_replace("/". $pattern ."/", '', $translation);
    }
}
