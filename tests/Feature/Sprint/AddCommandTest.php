<?php

use App\Services\SprintService;

it('adds issues to sprint', function () {
    $sprintService = Mockery::mock(SprintService::class);
    $sprintService->shouldReceive('addIssues')
        ->with(2, ['PROJ-1', 'PROJ-2'])
        ->once()
        ->andReturn([]);
    $this->app->instance(SprintService::class, $sprintService);

    $this->artisan('sprint:add 2 PROJ-1 PROJ-2')
        ->expectsOutputToContain('Added 2 issue(s)')
        ->assertExitCode(0);
});
