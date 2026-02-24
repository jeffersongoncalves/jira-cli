<?php

use App\Services\IssueService;

it('adds comment with --body option', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('addComment')
        ->with('PROJ-1', 'This is a test comment')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:comment PROJ-1 --body="This is a test comment"')
        ->expectsOutputToContain('Comment added')
        ->assertExitCode(0);
});
