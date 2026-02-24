<?php

use App\Services\IssueService;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fallbackWhen(true);
});

it('creates an issue with options', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('create')
        ->withAnyArgs()
        ->once()
        ->andReturn(loadFixture('issue-created'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:create --project=PROJ --type=Bug --summary="Fix login" --priority=High --description="Test"')
        ->expectsOutputToContain('PROJ-3')
        ->assertExitCode(0);
});
