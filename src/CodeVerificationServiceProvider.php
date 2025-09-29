<?php declare(strict_types=1);
namespace Codedive\LaravelCodeVerification;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

/**
 * Verification Code Service Provider
 */
class CodeVerificationServiceProvider extends ServiceProvider
{
    /**
     * Register
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/code_verification.php', 'code_verification'
        );

        // register service
        $this->app->singleton(CodeVerificationService::class, function ($app) {
            return new CodeVerificationService($app['config']['code_verification']);
        });
    }

    /**
     * Boot
     *
     * @return void
     */
    public function boot(): void
    {
        // language publish
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'code_verification');

        // database migrate
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // config publish
        $this->publishes([
            __DIR__.'/../config/code_verification.php' => config_path('code_verification.php'),
        ], 'code-verification-config');

        // language publish
        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/code_verification'),
        ], 'code-verification-lang');

        // migration publish
        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'code-verification-migrations');

    }
}
