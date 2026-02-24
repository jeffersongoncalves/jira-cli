<?php

use App\Services\EpicService;

it('adds issues to epic', function () {
    $epicService = Mockery::mock(EpicService::class);
    $epicService->shouldReceive('addIssues')
        ->with('PROJ-100', ['PROJ-1', 'PROJ-2'])
        ->once()
        ->andReturn([]);
    $this->app->instance(EpicService::class, $epicService);

    $this->artisan('epic:add PROJ-100 PROJ-1 PROJ-2')
        ->expectsOutputToContain('Added 2 issue(s)')
        ->assertExitCode(0);
});
