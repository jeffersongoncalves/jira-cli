<?php

use App\Services\IssueService;

it('clones an issue', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('cloneIssue')
        ->withAnyArgs()
        ->once()
        ->andReturn(loadFixture('issue-created'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:clone PROJ-1')
        ->expectsOutputToContain('PROJ-3')
        ->assertExitCode(0);
});
