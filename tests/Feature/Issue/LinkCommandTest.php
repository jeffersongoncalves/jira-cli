<?php

use App\Services\IssueService;

it('links two issues with --type option', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('link')
        ->with('Blocks', 'PROJ-1', 'PROJ-2')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:link PROJ-1 PROJ-2 --type=Blocks')
        ->expectsOutputToContain('Linked')
        ->assertExitCode(0);
});
