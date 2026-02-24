<?php

use App\Services\SprintService;

it('lists sprints', function () {
    $sprintService = Mockery::mock(SprintService::class);
    $sprintService->shouldReceive('list')
        ->with(1, null)
        ->andReturn(loadFixture('sprints'));
    $this->app->instance(SprintService::class, $sprintService);

    $this->artisan('sprint:list --board=1')
        ->expectsOutputToContain('Sprint 1')
        ->expectsOutputToContain('Sprint 2')
        ->assertExitCode(0);
});

it('requires board option', function () {
    $sprintService = Mockery::mock(SprintService::class);
    $this->app->instance(SprintService::class, $sprintService);

    $this->artisan('sprint:list')
        ->expectsOutputToContain('Board ID is required')
        ->assertExitCode(1);
});
