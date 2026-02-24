<?php

use App\Services\IssueService;

it('adds worklog with options', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('addWorklog')
        ->withAnyArgs()
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:worklog PROJ-1 --time=2h')
        ->expectsOutputToContain('Worklog added')
        ->assertExitCode(0);
});

it('lists worklogs', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('getWorklogs')
        ->withAnyArgs()
        ->andReturn(loadFixture('worklogs'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:worklog PROJ-1 --list')
        ->expectsOutputToContain('10001')
        ->assertExitCode(0);
});
