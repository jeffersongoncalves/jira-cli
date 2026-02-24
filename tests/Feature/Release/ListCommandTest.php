<?php

use App\Services\ReleaseService;

it('lists releases', function () {
    $releaseService = Mockery::mock(ReleaseService::class);
    $releaseService->shouldReceive('list')
        ->withAnyArgs()
        ->andReturn(loadFixture('releases'));
    $this->app->instance(ReleaseService::class, $releaseService);

    $this->artisan('release:list --project=PROJ')
        ->expectsOutputToContain('v1.0.0')
        ->assertExitCode(0);
});

it('requires project option', function () {
    $releaseService = Mockery::mock(ReleaseService::class);
    $this->app->instance(ReleaseService::class, $releaseService);

    $this->artisan('release:list')
        ->expectsOutputToContain('Project key is required')
        ->assertExitCode(1);
});
