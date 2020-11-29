<?php

namespace Yormy\TranslationcaptainLaravel\Tests\Features;

use Illuminate\Support\Facades\Storage;
use Yormy\TranslationcaptainLaravel\Exceptions\MissingTranslationException;
use Yormy\TranslationcaptainLaravel\Observers\Events\MissingTranslationEvent;
use Yormy\TranslationcaptainLaravel\Services\FileWriters\GeneratorVue;
use Yormy\TranslationcaptainLaravel\Services\PushService;
use Yormy\TranslationcaptainLaravel\Tests\TestCase;

class TranslatorTest extends TestCase
{
    protected $translationsRead;

    protected $locales = ['nl','en','xx']; // test also non existing locale

    const LANG_DIR = 'lang_tc_vuw';

//    public function setUp(): void
//    {
//        parent::setUp();
//
//        $pull = new PushService($this->locales);
//        $allKeys = $pull->getAllKeys();
//
//        $this->translationsRead = $allKeys;
//
//        $bladeFilesGenerator = new GeneratorVue($this->translationsRead);
//        $bladeFilesGenerator->export($this->locales);
//    }

    /** @test */
    public function key_is_translated()
    {
        $translated = __('auth.failed');
        $this->assertEquals($translated, 'These credentials do not match our records.');
    }

    /** @test */
    public function missing_key_exception_thrown()
    {
        try {
            $this->report(__('this-key-does-not-exist'));
            $this->assertTrue(false);
        } catch (MissingTranslationException $e) {
            $this->assertTrue(true);
        }
    }

    /** @test */
    public function missing_key_no_exception_thrown()
    {
        config(['translationcaptain-laravel.exceptions.on_missing_key' => false]);
        $this->expectsEvents(MissingTranslationEvent::class);
        $this->report(__('this-key-does-not-exist'));
        $this->assertTrue(true);
    }

    /** @test */
    public function missing_key_added_for_uploading()
    {
        // added to log & added to queu
        config(['translationcaptain-laravel.exceptions.on_missing_key' => false]);

        $queueFilename = config('translationcaptain-laravel.queue_filename');
        Storage::delete($queueFilename);

        $this->report(__('this-key-does-not-exist'));
        $this->report(__('this-key-does-also-not-exist'));

        if (! Storage::exists($queueFilename)) {
            $this->assertTrue(false);
        } else {
            $fileContents = Storage::disk('local')->get($queueFilename);
            $this->assertStringContainsString("__('this-key-does-not-exist')", $fileContents);
            $this->assertStringContainsString("__('this-key-does-also-not-exist')", $fileContents);
        }
    }
}
