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
                $this->assertFileExists($filename);
            }
        }
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










    /**
     * =========== HELPER FUNCTIONS ================
     */
    private function assertArrayHasKeyLocales($key, $locales = null)
    {
        if (!$locales) {
            $locales = $this->locales;
        }
        foreach ($locales as $locale) {
            $this->assertArrayHasKey("$locale.$key", $this->translationsRead);
        }
    }

    private function assertKeyHasValue($key, $value)
    {
        $this->assertEquals($this->translationsRead[$key] , $value);
    }

    private function assertNewKeyHasPrefixedValue(string $locale, string $key)
    {
        $this->assertKeyHasValue("$locale.$key", "#$key");
    }

}
