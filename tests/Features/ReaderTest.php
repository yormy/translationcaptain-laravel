<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Illuminate\Support\Arr;

use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class ReaderTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en'];

    public function setUp(): void
    {
        parent::setUp();


        $pull = new PushService($this->locales);
        $allKeys = $pull->getAllKeys();

        $this->translationsRead = Arr::dot($allKeys);
    }

    /** @test */
    public function test()
    {
        $this->assertArrayHasKey('de.multilingual-admin::apicode.cannot_delete_non_base', $this->translationsRead);
    }

    /** @test */
    public function found_new_key_in_scanned_blade_source()
    {
        foreach ($this->locales as $locale) {
            $this->assertArrayHasKey($locale. '.app.welcome.found', $this->translationsRead);
            $this->assertArrayHasKey($locale. '.app.home.found', $this->translationsRead);
            $this->assertArrayHasKey($locale. '.app.login.found', $this->translationsRead);
            $this->assertArrayHasKey($locale. '.app.developer.performance.index.found', $this->translationsRead);
        }
    }
}
