<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Yormy\TranslationcaptainLaravel\Services\FileWriters\WriterBlade;
use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class WriterBladeTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en','xx']; // test also non existing locale


    protected $exportPath;

    public function setUp(): void
    {
        parent::setUp();

        $push = new PushService($this->locales);
        $allKeys = $push->getAllKeys();

        $this->translationsRead = $allKeys;

        $this->exportPath = './tests/Features/Data/Exports/Blade/lang_blade';

        $writer = new WriterBlade();
        $writer->setExportPath($this->exportPath);

        $writer->setLabels($this->translationsRead);
        // $bladeFilesGenerator->zipCurrentFiles();
        $writer->export($this->locales);
    }

    /** @test */
    public function blade_files_generated_plain_and_vendor()
    {
        foreach ($this->locales as $locale) {
            foreach (array_keys($this->translationsRead[$locale]) as $file) {
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
        if (strpos($file, '::') === false) {
            $filename = $this->exportPath.
                DIRECTORY_SEPARATOR .
                DIRECTORY_SEPARATOR .
                $locale .
                DIRECTORY_SEPARATOR .
                $file .
                ".php";
        } else {
            $vendorSepPos = strpos($file, '::');
            $vendorName = substr($file, 0, $vendorSepPos);
            $filenameWithoutVendor = substr($file, $vendorSepPos + 2);

            $filename = $this->exportPath.
                DIRECTORY_SEPARATOR.
                'vendor'.
                DIRECTORY_SEPARATOR.
                $vendorName.
                DIRECTORY_SEPARATOR.
                $locale .
                DIRECTORY_SEPARATOR.
                $filenameWithoutVendor.
                ".php";
        }

        return $filename;
    }

    public function generateFilenameSingleFileTranslation(string $locale): string
    {
        return $this->exportPath.
            DIRECTORY_SEPARATOR.
            $locale.
            ".php";
    }
}
