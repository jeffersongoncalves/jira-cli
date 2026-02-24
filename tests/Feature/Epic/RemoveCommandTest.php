<?php

use App\Services\EpicService;

it('removes issues from epic', function () {
    $epicService = Mockery::mock(EpicService::class);
    $epicService->shouldReceive('removeIssues')
        ->with(['PROJ-1'])
        ->once()
        ->andReturn([]);
    $this->app->instance(EpicService::class, $epicService);

    $this->artisan('epic:remove PROJ-1')
        ->expectsOutputToContain('Removed 1 issue(s)')
        ->assertExitCode(0);
});
