<?php

use App\Services\SprintService;

it('closes sprint with --force', function () {
    $sprintService = Mockery::mock(SprintService::class);
    $sprintService->shouldReceive('close')
        ->with(2)
        ->once()
        ->andReturn([]);
    $this->app->instance(SprintService::class, $sprintService);

    $this->artisan('sprint:close 2 --force')
        ->expectsOutputToContain('closed')
        ->assertExitCode(0);
});
