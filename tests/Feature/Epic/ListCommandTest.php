<?php

use App\Services\EpicService;

it('lists epics', function () {
    $epicService = Mockery::mock(EpicService::class);
    $epicService->shouldReceive('list')
        ->withAnyArgs()
        ->andReturn(loadFixture('epics'));
    $this->app->instance(EpicService::class, $epicService);

    $this->artisan('epic:list --project=PROJ')
        ->expectsOutputToContain('PROJ-100')
        ->assertExitCode(0);
});

it('requires project option', function () {
    $epicService = Mockery::mock(EpicService::class);
    $this->app->instance(EpicService::class, $epicService);

    $this->artisan('epic:list')
        ->expectsOutputToContain('Project key is required')
        ->assertExitCode(1);
});
