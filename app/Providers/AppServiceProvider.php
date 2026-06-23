<?php

namespace App\Providers;

use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;
use JeffersonGoncalves\LaravelZero\SelfUpdate\PharUpdater;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void {}

    public function register(): void
    {
        $this->app->singleton(AuthService::class);

        $this->app->singleton(PharUpdater::class, fn () => new PharUpdater(
            githubRepo: 'jeffersongoncalves/jira-cli',
            assetName: 'jira.phar',
            tempPrefix: 'jira_',
            currentVersion: (string) config('app.version', 'unreleased'),
        ));
    }
}
