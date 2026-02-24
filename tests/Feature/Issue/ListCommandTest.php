<?php

use App\Services\IssueService;

it('lists issues', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('search')
        ->withAnyArgs()
        ->andReturn(loadFixture('issues-search'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:list --project=PROJ')
        ->expectsOutputToContain('PROJ-1')
        ->assertExitCode(0);
});

it('shows message when no issues found', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('search')
        ->andReturn(['issues' => []]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:list --project=PROJ')
        ->expectsOutputToContain('No results found')
        ->assertExitCode(0);
});
