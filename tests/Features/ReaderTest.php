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

        $push = new PushService($this->locales);
        $allKeys = $push->getAllKeys();

        $this->translationsRead = Arr::dot($allKeys);
    }

    /** @test */
    public function found_key_in_translations_subdirectory()
    {
        $this->assertArrayHasKeyLocales('menus/admin/manage/admin.translations.title', ['en','nl']);
    }

    /** @test */
    public function found_key_in_translations_vendor()
    {
        $this->assertArrayHasKeyLocales('yormy::level1/rr/messages.expired.title', ['en']);
    }

    /** @test */
    public function found_key_in_translations_deep()
    {
        $this->assertArrayHasKeyLocales('action.another.boom.kayak.boom.surfer', ['en']);
    }

    /** @test */
    public function found_key_in_translations_singlefile()
    {
        $this->assertArrayHasKeyLocales('___.default_single_file_translations', ['en']);
    }

    /** @test */
    public function found_key_in_json()
    {
        $this->assertArrayHasKeyLocales('billing.plans.monthly.title', ['en']);
        $this->assertArrayHasKeyLocales('billing.plans.monthly.description', ['en']);
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
        $this->assertNewKeyHasPrefixedValue('xx', "app.welcome.found");
    }

    /** @test */
    public function key_has_translation()
    {
        $this->assertKeyHasValue('nl.billing.plans.monthly.title', 'Maandelijkse betaal plan');
    }

    /** @test */
    public function key_blade_has_bindings()
    {
        $this->assertKeyHasValue(
            'en.yormy::level1/rr/notifications.mail.message',
            '%%middleware%% attack on %%domain%% has from %%ip%% address. affected: %%url%%'
        );
    }

    /** @test */
    public function key_json_has_bindings()
    {
        $this->assertKeyHasValue(
            'en.billing.plans.monthly.description',
            'The %%_field_%% field must be %%width%% pixels by %%height%% %%height%% pixels'
        );
    }

    /** @test */
    public function found_key_without_dot()
    {
        $this->assertArrayHasKeyLocales('___.key-without-dot', ['en']);
        $this->assertNewKeyHasPrefixedValue('xx', '___.key-without-dot');
    }

    /**
     * =========== HELPER FUNCTIONS ================
     */
    private function assertArrayHasKeyLocales($key, $locales = null)
    {
        if (! $locales) {
            $locales = $this->locales;
        }
        foreach ($locales as $locale) {
            $this->assertArrayHasKey("$locale.$key", $this->translationsRead);
        }
    }

    private function assertKeyHasValue($key, $value)
    {
        $this->assertEquals($this->translationsRead[$key], $value);
    }

    private function assertNewKeyHasPrefixedValue(string $locale, string $key)
    {
        $this->assertKeyHasValue("$locale.$key", "#$key");
    }
}
