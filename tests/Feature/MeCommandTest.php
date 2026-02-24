<?php

use App\Services\JiraService;

it('shows current user', function () {
    $jiraService = Mockery::mock(JiraService::class);
    $jiraService->shouldReceive('restApi')->with('myself')->andReturn('/rest/api/3/myself');
    $jiraService->shouldReceive('get')
        ->with('/rest/api/3/myself')
        ->andReturn(loadFixture('user'));
    $this->app->instance(JiraService::class, $jiraService);

    $this->artisan('me')
        ->expectsOutputToContain('John Doe')
        ->expectsOutputToContain('john@example.com')
        ->assertExitCode(0);
});
