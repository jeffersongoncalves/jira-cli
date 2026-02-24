<?php

use App\Services\IssueService;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fallbackWhen(true);
});

it('moves issue with --status option', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('getTransitions')
        ->with('PROJ-1')
        ->andReturn(loadFixture('issue-transitions'));
    $issueService->shouldReceive('transition')
        ->with('PROJ-1', '31')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:move PROJ-1 --status=Done')
        ->expectsOutputToContain('moved to')
        ->assertExitCode(0);
});

it('shows error for invalid status', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('getTransitions')
        ->with('PROJ-1')
        ->andReturn(loadFixture('issue-transitions'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:move PROJ-1 --status=Invalid')
        ->expectsOutputToContain('not found')
        ->assertExitCode(1);
});
