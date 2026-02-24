<?php

use App\Services\IssueService;

it('shows issue details', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('get')
        ->with('PROJ-1')
        ->andReturn(loadFixture('issue-single'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:view PROJ-1')
        ->expectsOutputToContain('PROJ-1')
        ->expectsOutputToContain('Bug')
        ->assertExitCode(0);
});

it('shows comments when requested', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('get')
        ->with('PROJ-1')
        ->andReturn(loadFixture('issue-single'));
    $issueService->shouldReceive('getComments')
        ->with('PROJ-1')
        ->andReturn(loadFixture('comments'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:view PROJ-1 --comments')
        ->expectsOutputToContain('Working on this now')
        ->assertExitCode(0);
});
