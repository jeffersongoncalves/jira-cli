<?php

use App\Services\IssueService;

it('edits an issue', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('edit')
        ->with('PROJ-1', Mockery::type('array'))
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:edit PROJ-1 --summary="Updated summary"')
        ->expectsOutputToContain('PROJ-1 updated')
        ->assertExitCode(0);
});

it('shows warning when no fields provided', function () {
    $issueService = Mockery::mock(IssueService::class);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:edit PROJ-1')
        ->expectsOutputToContain('No fields to update')
        ->assertExitCode(1);
});
