<?php

use App\Services\IssueService;

it('deletes issue with --force', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('delete')
        ->with('PROJ-1')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:delete PROJ-1 --force')
        ->expectsOutputToContain('deleted')
        ->assertExitCode(0);
});
