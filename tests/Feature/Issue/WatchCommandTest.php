<?php

use App\Services\IssueService;
use App\Services\JiraService;

it('watches an issue', function () {
    $jiraService = Mockery::mock(JiraService::class);
    $jiraService->shouldReceive('get')->andReturn(loadFixture('user'));
    $jiraService->shouldReceive('restApi')->with('myself')->andReturn('/rest/api/3/myself');
    $this->app->instance(JiraService::class, $jiraService);

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('watch')
        ->with('PROJ-1', 'abc123')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:watch PROJ-1')
        ->expectsOutputToContain('watching')
        ->assertExitCode(0);
});
