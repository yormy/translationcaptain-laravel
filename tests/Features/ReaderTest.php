<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Illuminate\Support\Arr;

use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class ReaderTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en','xx']; // test also non existing locale

    public function setUp(): void
    {
        parent::setUp();


        $pull = new PushService($this->locales);
        $allKeys = $pull->getAllKeys();

        $this->translationsRead = Arr::dot($allKeys);

        //dd($this->translationsRead);
    }

    /** @test */
    public function found_key_in_translations_subdirectory()
    {
        $this->assertArrayHasKeyLocales('menus/admin/manage/admin.log', ['en']);
    }

    /** @test */
    public function found_key_in_translations_vendor()
    {
        $this->assertArrayHasKeyLocales('firewall::level1/rr/notifications.mail.subject', ['en']);
    }

    /** @test */
    public function found_key_in_translations_deep()
    {
        $this->assertArrayHasKeyLocales('action.another_test.test.level2', ['en']);
    }

    /** @test */
    public function found_key_in_translations_singlefile()
    {
        $this->markTestIncomplete('This test has not been implemented yet.', ['en']);
    }


    /** @test */
    public function found_new_key_in_scanned_blade_source()
    {
        $this->assertArrayHasKeyLocales('app.welcome.found');
        $this->assertArrayHasKeyLocales('app.home.found');
        $this->assertArrayHasKeyLocales('app.login.found');
        $this->assertArrayHasKeyLocales('app.developer.performance.index.found');
    }

    /** @test */
    public function key_added_to_non_existing_language()
    {
        $this->assertArrayHasKeyLocales('app.welcome.found', ['xx']);
        $this->assertNewKeyHasPrefixedValue('xx',"app.welcome.found");
    }

    /** @test */
    public function key_has_translation()
    {
       $this->assertKeyHasValue('en.validations.alpha', 'The %%_field_%% field may only contain alphabetic characters');
    }

    /** @test */
    public function key_has_binding_eol()
    {
        $this->assertKeyHasValue('en.firewall::level1/rr/notifications.mail.message',
            'A possible %%middleware%% attack on %%domain%% has been detected from %%ip%% address. The following URL has been affected: %%url%%');
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
