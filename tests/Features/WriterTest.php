<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Illuminate\Support\Arr;

use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorBlade;
use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class WriterTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en','xx']; // test also non existing locale

    const LANG_DIR = 'lang_tc';

    public function setUp(): void
    {
        parent::setUp();

        $pull = new PushService($this->locales);
        $allKeys = $pull->getAllKeys();

        $this->translationsRead = $this->fakeExported($allKeys);
    }

    private function fakeExported($allkeys)
    {
        return $allkeys;
    }



    /** @test */
    public function blade_files_generated_plan_and_vendor()
    {
        $bladeFilesGenerator = new GeneratorBlade($this->translationsRead);
        $bladeFilesGenerator->export($this->locales);

        foreach ($this->locales as $locale) {
            foreach ($this->translationsRead[$locale] as $file => $content) {

                $filename = $this->generateFilename($locale, $file);
                if (false === strpos($filename, "___.php")) {
                    $this->assertFileExists($filename);
                }
            }
        }
    }

    /** @test */
    public function blade_files_contains_translation()
    {
        $filename = $this->generateFilename('en', 'action');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('another.boom.kayak.boom.surfer', $fileContents);
        $this->assertStringContainsString('key_defined_in_blade_and_vue', $fileContents);
        $this->assertStringContainsString('this key is defined in blade and vue with same translation', $fileContents);
    }

    /** @test */
    public function blade_files_contains_translation_from_json()
    {
        $filename = $this->generateFilename('en', 'billing');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('plans.monthly.description', $fileContents);
        $this->assertStringContainsString('The :_field_ field must be :_field_ pixels by :_field_ :_field_ pixels', $fileContents);
    }

    /** @test */
    public function blade_files_single_file_translation_generated()
    {
        foreach ($this->locales as $locale) {
            $filename = $this->generateFilenameSingleFileTranslation($locale);
            $this->assertFileExists($filename);
        }
    }

    /** @test */
    public function blade_files_single_file_translation_content()
    {
        $filename = $this->generateFilenameSingleFileTranslation('en');
        $fileContents = file_get_contents($filename);
        $this->assertStringContainsString('default_single_file_translations', $fileContents);
        $this->assertStringContainsString('#___.key-without-dot', $fileContents);
    }

    /** @test */
    public function blade_vendor_files_contains_translation()
    {
        $filename = $this->generateFilename('en', 'yormy::level1/rr/messages');
        $fileContents = file_get_contents($filename);

        $this->assertStringContainsString('expired.title', $fileContents);
        $this->assertStringContainsString('The invite code :CODE has expired.', $fileContents);
    }

    public function generateFilename(string $locale, string $file): string
    {
        if(strpos($file, '::') === false) {
            $filename = base_path() . DIRECTORY_SEPARATOR .
                'resources' .
                DIRECTORY_SEPARATOR .
                self::LANG_DIR .
                DIRECTORY_SEPARATOR .
                $locale .
                DIRECTORY_SEPARATOR .
                $file .
                ".php";
        } else {

            $vendorSepPos = strpos($file, '::');
            $vendorName = substr($file, 0, $vendorSepPos);
            $filenameWithoutVendor = substr($file, $vendorSepPos+2);

            $filename = base_path() . DIRECTORY_SEPARATOR .
                'resources' .
                DIRECTORY_SEPARATOR.
                self::LANG_DIR .
                DIRECTORY_SEPARATOR.
                'vendor'.
                DIRECTORY_SEPARATOR.
                $vendorName.
                DIRECTORY_SEPARATOR.
                $locale .
                DIRECTORY_SEPARATOR .
                $filenameWithoutVendor .
                ".php";
        }
        return $filename;
    }


    public function generateFilenameSingleFileTranslation(string $locale): string
    {
        return base_path() . DIRECTORY_SEPARATOR .
            'resources' .
            DIRECTORY_SEPARATOR .
            self::LANG_DIR .
            DIRECTORY_SEPARATOR .
            $locale .
            ".php";
    }

}
