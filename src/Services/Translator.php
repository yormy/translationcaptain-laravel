<?php

namespace Yormy\TranslationcaptainLaravel\Services;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Translation\Translator as BaseTranslator;
use Yormy\TranslationcaptainLaravel\Exceptions\MissingTranslationException;
use Yormy\TranslationcaptainLaravel\Observers\Events\MissingTranslationEvent;

class Translator extends BaseTranslator
{
    private $cookieContent;

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

        if (!config('translationcaptain-laravel.enabled')) {
            return $translation;
        }

        $isMissing = $this->translationMissing($key, $translation, $locale);
        $this->persistKeyForContext($key, !$isMissing);

        if ($isMissing) {
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

    public function persistKeyForContext(string $key, bool $isExisting) : void
    {
        if (!$this->canCollect($key, $isExisting)) {
            return;
        }

        $cookieKey = config("translationcaptain-laravel.cookie.collect");
        $contextItems = Cookie::get($cookieKey);

        if (!is_array($this->cookieContent) || !in_array($key, $this->cookieContent)) {
            $this->cookieContent[] = $key;
        }

        // non secure, non encrypted cookie because the frontend needs to be able to read them
        Cookie::queue($cookieKey, json_encode($this->cookieContent), 1, null, null, false, false);
    }

    private function canCollect(string $key, bool $isExisting) : bool
    {
        $enabled = config("translationcaptain-laravel.screenshot_collect_trigger", false);
        if (!$enabled || $enabled === "NONE") {
            return false;
        }

        $enabledByCookie = Cookie::get(config("translationcaptain-laravel.cookie.screenshot_enabled"));

        if ($enabled === "ON_ENABLED_COOKIE" && !$enabledByCookie) {
            return false;
        }

        $contextCollectItems = config("translationcaptain-laravel.screenshot_collect_for", false);
        if($contextCollectItems !== "ALL" && $isExisting) {
            return false;
        }

        if (!$this->includedPath() ||
            !$this->includedRoute() ||
            !$this->includedKey($key)
        ) {
            return false;
        }

        return true;
    }


    private function includedPath() : bool
    {
        $path = request()->path();
        $path = "/". $path;

        $excludes = config("translationcaptain-laravel.exclude.urls");
        if (in_array($path, $excludes)) {
            return false;
        }
        return true;
    }

    private function includedKey(string $key) : bool
    {
        $excludes = config("translationcaptain-laravel.exclude.keys");
        if (in_array($key, $excludes)) {
            return false;
        }
        return true;
    }

    private function includedRoute() : bool
    {
        $route = request()->route()->getName();
        $excludes = config("translationcaptain-laravel.exclude.routes");
        if (in_array($route, $excludes)) {
            return false;
        }
        return true;
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
