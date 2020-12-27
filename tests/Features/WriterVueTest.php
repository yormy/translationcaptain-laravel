<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Yormy\TranslationcaptainLaravel\Services\FileWriters\WriterVue;
use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class WriterVueTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en','xx']; // test also non existing locale

    const LANG_DIR = 'lang_tc_vuw';

    public function setUp(): void
    {
        parent::setUp();

        $push = new PushService($this->locales);
        $allKeys = $push->getAllKeys();

        $this->translationsRead = $allKeys;

        $writer = new WriterVue();
        $writer->setLabels($this->translationsRead);
        $writer->export($this->locales);
    }

    /** @test */
    public function vue_files_generated()
    {
        foreach ($this->locales as $locale) {
            foreach (array_keys($this->translationsRead[$locale]) as $file) {
                $filename = $this->generateFilename($locale, $file);
                if (false === strpos($filename, "___.json")) {
                    $this->assertFileExists($filename);
                }
            }
        }
    }

    /** @test */
    public function vue_files_contains_translation()
    {
        $filename = $this->generateFilename('en', 'action');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('"another.boom.surfer"', $fileContents);
        $this->assertStringContainsString('"#action.key_also_in_source"', $fileContents);
    }

    /** @test */
    public function vue_files_contains_translation_bindings()
    {
        $filename = $this->generateFilename('en', 'billing');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('"The {_field_} field must be {_field_} pixels by {_field_} {_field_} pixels"', $fileContents);
    }

    /** @test */
    public function blade_files_contains_translation_nested()
    {
        $filename = $this->generateFilename('en', 'yormy::level1/rr/messages');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('"The invite code {CODE} has expired."', $fileContents);
    }

    /**
     * ===================== HELPERS ====================
     */
    public function generateFilename(string $locale, string $file): string
    {
        return base_path() . DIRECTORY_SEPARATOR .
            'resources' .
            DIRECTORY_SEPARATOR .
            self::LANG_DIR .
            DIRECTORY_SEPARATOR .
            $locale .
            DIRECTORY_SEPARATOR .
            $file .
            ".json";
    }
}
