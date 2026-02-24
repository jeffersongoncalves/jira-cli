<?php

use App\Services\IssueService;

it('removes issue link', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('unlink')
        ->with('10000')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:unlink 10000')
        ->expectsOutputToContain('removed')
        ->assertExitCode(0);
});
