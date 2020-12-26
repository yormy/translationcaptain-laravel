<?php

namespace Yormy\TranslationcaptainLaravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Yormy\TranslationcaptainLaravel\Commands\PullCommand;
use Yormy\TranslationcaptainLaravel\Commands\PushCommand;
use Yormy\TranslationcaptainLaravel\Commands\SyncCommand;
use Yormy\TranslationcaptainLaravel\Http\Controllers\PullController;
use Yormy\TranslationcaptainLaravel\Http\Controllers\PushController;
use Yormy\TranslationcaptainLaravel\Providers\EventServiceProvider;
use Yormy\TranslationcaptainLaravel\Providers\TranslationServiceProvider;

class TranslationcaptainLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/translationcaptain.php' => config_path('translationcaptain.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views/blade' => base_path('resources/views/vendor/translationcaptain'),
            ], 'blade');

            $this->publishes([
                __DIR__ . '/../resources/views/vue' => base_path('resources/views/vendor/translationcaptain'),
                __DIR__ . '/../resources/assets' => resource_path('assets/vendor/translationcaptain'),
            ], 'vue');

            $this->publishMigrations();

            $this->commands([
                PushCommand::class,
                PullCommand::class,
                SyncCommand::class,
            ]);

            $ui_type = 'blade';
        } else {
            $ui_type = 'blade';
            if ("VUE" === config('translationcaptain.ui_type')) {
                $ui_type = 'vue';
            }
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views/'. $ui_type, 'translationcaptain');

        $this->registerGuestRoutes();
        $this->registerUserRoutes();
        $this->registerAdminRoutes();

        $this->app->make('config')->set('logging.channels.translationcaptain', [
            'driver' => 'daily',
            'path' => storage_path('logs/translationcaptain.log'),
            'level' => 'debug',
            'days' => 31,
        ]);
    }

    private function publishMigrations()
    {
        $migrations = [
            'create_referral_actions_table.php',
            'create_referral_domains_table.php',
            'create_referral_payments_table.php',
            'create_referral_awards_table.php',
            'seed_referral_actions_table.php',
        ];

        $index = 0;
        foreach ($migrations as $migrationFileName) {
            if (! $this->migrationFileExists($migrationFileName)) {
                $sequence = date('Y_m_d_His', time());
                $newSequence = substr($sequence, 0, strlen($sequence) - 2);
                $paddedIndex = str_pad(strval($index), 2, '0', STR_PAD_LEFT);
                $newSequence .= $paddedIndex;
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . $newSequence . '_' . $migrationFileName),
                ], 'migrations');

                $index++;
            }
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/translationcaptain.php', 'translationcaptain');
        $this->app->register(EventServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
    }

    private function registerGuestRoutes()
    {
        Route::macro('Translationcaptain', function (string $prefix) {
            Route::prefix($prefix)->name($prefix. ".")->group(function () {
                Route::get('/push', [PushController::class, 'push'])->name('push');
                Route::get('/pull', [PullController::class, 'pull'])->name('pull');
            });
        });
    }

    private function registerUserRoutes()
    {
//        Route::macro('TranslationcaptainLaravelUser', function (string $prefix) {
//            Route::prefix($prefix)->name($prefix. ".")->group(function () {
//                Route::get('/details', [ReferrerDetailsController::class, 'show'])->name('show');
//            });
//        });
    }

    private function registerAdminRoutes()
    {
        //  Route::get('/admin1/ref/details/{referrer}', [ReferrerDetailsController::class, 'showForUser'])->name('shownow');

//        Route::macro('TranslationcaptainLaravelAdmin', function (string $prefix) {
//            Route::prefix($prefix)->name($prefix. ".")->group(function () {
//                Route::get('/referrers', [ReferrerOverviewController::class, 'index'])->name('overview');
//                Route::get('/referrers/{referrer}', [ReferrerDetailsController::class, 'showForUser'])->name('showForUser');
//            });
//        });
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}
