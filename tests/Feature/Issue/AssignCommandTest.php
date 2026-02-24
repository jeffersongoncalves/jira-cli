<?php

use App\Services\IssueService;
use App\Services\JiraService;

it('assigns issue to user', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('assign')
        ->with('PROJ-1', 'abc123')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $jiraService = Mockery::mock(JiraService::class);
    $this->app->instance(JiraService::class, $jiraService);

    $this->artisan('issue:assign PROJ-1 abc123')
        ->expectsOutputToContain('assigned')
        ->assertExitCode(0);
});

it('unassigns issue when no user given', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('assign')
        ->with('PROJ-1', null)
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $jiraService = Mockery::mock(JiraService::class);
    $this->app->instance(JiraService::class, $jiraService);

    $this->artisan('issue:assign PROJ-1')
        ->expectsOutputToContain('unassigned')
        ->assertExitCode(0);
});
