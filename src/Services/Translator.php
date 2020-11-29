<?php

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Translation\Translator as BaseTranslator;
use Yormy\TranslationcaptainLaravel\Exceptions\MissingTranslationException;
use Yormy\TranslationcaptainLaravel\Observers\Events\MissingTranslationEvent;

class Translator extends BaseTranslator
{
    /**
     * Get the translation for the given key.
     *
     * This method acts as a pass-through to Illuminate\Translation\Translator::get(), but verifies
     * that a replacement has actually been made.
     *
     * @throws MissingTranslationException When no replacement is made.
     *
     * @return string|array|null
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        if ($this->translationMissing($key, $translation, $locale)) {
            if (config('translationcaptain-laravel.log_missing_keys')) {
                $this->logMissingTranslation($key, $replace, $locale, $fallback);
            }

            $this->addToQueueForUploading($key);

            if (config('translationcaptain-laravel.exceptions.on_missing_key')) {
                throw new MissingTranslationException($key);
            }

            event(new MissingTranslationEvent($key, $replace, $locale, $fallback));
        }

        return $translation;
    }

    private function translationMissing(string $key, string $translation, ?string $locale) : bool
    {
        $isDefaultLocale = ($locale === config('translationcaptain-laravel.default_locale'));

        // if ($translation === $key && !$isDefaultLocale) {
        if ($translation === $key) {
            return true;
        }

        return false;
    }

    private function addToQueueForUploading(string $key) : void
    {
        $queueFilename = config('translationcaptain-laravel.queue_filename');

        $formattedKey = $this->formatKeyForQueue($key);

        if (! Storage::exists($queueFilename)) {
            Storage::disk('local')->append($queueFilename, $formattedKey);

            return;
        }

        $currentQueue = Storage::get($queueFilename);
        if (false === strpos($currentQueue, $formattedKey)) {
            Storage::disk('local')->append($queueFilename, $formattedKey);
        }
    }

    protected function formatKeyForQueue(string $key) : string
    {
        return "__('$key')";
    }

    protected function logMissingTranslation(string $key, array $replace, ?string $locale, bool $fallback) : void
    {
        $message = 'Missing translation: ' . $key;
        Log::channel('translationcaptain')->info($message);
    }
}
