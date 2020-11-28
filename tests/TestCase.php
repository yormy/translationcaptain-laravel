<?php

namespace Yormy\TranslationcaptainLaravel\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Yormy\TranslationcaptainLaravel\Http\Middleware\ReferrerMiddleware;
use Yormy\TranslationcaptainLaravel\TranslationcaptainLaravelServiceProvider;

class TestCase extends Orchestra
{
    protected $prefix = 'ref';

    protected $userBob;
    protected $userAdam;
    protected $referrerFelix;

    public function setUp(): void
    {
        parent::setUp();

        // Note: this also flushes the cache from within the migration
        $this->setUpDatabase($this->app);

//        Route::middleware(ReferrerMiddleware::class)
//            ->group(function () {
//                Route::TranslationcaptainLaravelUser($this->prefix);
//                Route::TranslationcaptainLaravelAdmin($this->prefix);
//            });
//
//        $this->setViewForLayout();

        $this->overwriteConfigForTesting();
    }

    public function overwriteConfigForTesting()
    {
        $toAppRoot = "/../../../../";
        config(['translationcaptain-laravel.paths.blade' => $toAppRoot. 'tests/Features/Data/Translations/Blade/lang']);

        config(['translationcaptain-laravel.paths.vue' => $toAppRoot. 'tests/Features/Data/Translations/Vue/lang']);

        config(['translationcaptain-laravel.paths_sources.blade' => [
            $toAppRoot. 'tests/Features/Data/Sources/Blade',
            ]]);
    }

    public function get($uri, array $headers = [])
    {
        $uri = $this->prefix . $uri;

        return parent::get($uri, $headers);
    }

    protected function getPackageProviders($app)
    {
        return [
            TranslationcaptainLaravelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function report($message)
    {
        if (is_array($message) || is_object($message)) {
            fwrite(STDERR, (string)print_r($message)); // bool passed in.. how?
        } else {
            fwrite(STDERR, (string)$message);
        }
        fwrite(STDERR, PHP_EOL);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('name');
            $table->softDeletes();
        });
    }
}
