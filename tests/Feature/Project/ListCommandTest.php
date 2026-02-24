<?php

use App\Services\ProjectService;

it('lists projects', function () {
    $projectService = Mockery::mock(ProjectService::class);
    $projectService->shouldReceive('list')
        ->withAnyArgs()
        ->andReturn(loadFixture('projects'));
    $this->app->instance(ProjectService::class, $projectService);

    $this->artisan('project:list')
        ->expectsOutputToContain('PROJ')
        ->assertExitCode(0);
});
