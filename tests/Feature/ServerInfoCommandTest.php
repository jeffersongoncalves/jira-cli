<?php

use App\Services\JiraService;

it('shows server information', function () {
    $jiraService = Mockery::mock(JiraService::class);
    $jiraService->shouldReceive('restApi')->with('serverInfo')->andReturn('/rest/api/3/serverInfo');
    $jiraService->shouldReceive('get')
        ->with('/rest/api/3/serverInfo')
        ->andReturn(loadFixture('server-info'));
    $this->app->instance(JiraService::class, $jiraService);

    $this->artisan('serverinfo')
        ->expectsOutputToContain('example.atlassian.net')
        ->expectsOutputToContain('Cloud')
        ->assertExitCode(0);
});
