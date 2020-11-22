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

//        Factory::guessFactoryNamesUsing(
//            fn (string $modelName) => 'Yormy\\TranslationcaptainLaravel\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
//        );

        // Note: this also flushes the cache from within the migration
        $this->setUpDatabase($this->app);

        $this->userBob = $this->user('bob@user.com');
        $this->userAdam = $this->user('adam@user.com');
        $this->referrerFelix = $this->user('felix@referrer.com');


        Route::middleware(ReferrerMiddleware::class)
            ->group(function () {
                Route::TranslationcaptainLaravelUser($this->prefix);
                Route::TranslationcaptainLaravelAdmin($this->prefix);
            });

        $this->setViewForLayout();

        $this->overwriteConfigForTesting();
    }

    public function overwriteConfigForTesting()
    {
        config(['translationcaptain-laravel.models.referrer.public_id' => 'id']);

        config(['translationcaptain-laravel.models.referrer.class' => User::class]);
    }

    public function user(string $email)
    {
        return User::where('email', $email)->first();
    }

    private function setViewForLayout()
    {
        $viewPath = dirname(__DIR__);
        $viewPath .= "/resources/views";
        config(['view.paths' => [$viewPath]]);
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

//        include_once __DIR__.'/../database/migrations/create_referral_awards_table.php.stub';
//        (new \CreateReferralAwardsTable())->up();
//
//        include_once __DIR__.'/../database/migrations/create_referral_actions_table.php.stub';
//        (new \CreateReferralActionsTable())->up();
//
//        include_once __DIR__.'/../database/migrations/create_referral_domains_table.php.stub';
//        (new \CreateReferralDomainsTable())->up();
//
//        include_once __DIR__.'/../database/migrations/create_referral_payments_table.php.stub';
//        (new \CreateReferralPaymentsTable())->up();
//
//        include_once __DIR__.'/../database/migrations/seed_referral_actions_table.php.stub';
//        (new \SeedReferralActionsTable())->up();
//
//        User::create(['email' => 'bob@user.com', 'name' => 'bobuser']);
//        User::create(['email' => 'adam@user.com', 'name' => 'adamuser']);
//        User::create(['email' => 'felix@referrer.com', 'name' => 'felixreferrer']);
    }
}
