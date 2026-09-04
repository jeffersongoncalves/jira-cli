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

it('creates an issue with description from --description-file', function () {
    $file = tempnam(sys_get_temp_dir(), 'desc');
    file_put_contents($file, "### Heading\n\nBody text.");

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('create')
        ->with(Mockery::on(fn (array $fields) => $fields['description']['type'] === 'doc'))
        ->once()
        ->andReturn(loadFixture('issue-created'));
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:create', [
        '--project' => 'PROJ',
        '--type' => 'Bug',
        '--summary' => 'Fix login',
        '--description-file' => $file,
    ])
        ->expectsOutputToContain('PROJ-3')
        ->assertExitCode(0);

    unlink($file);
});

it('errors when --description-file does not exist', function () {
    $this->app->instance(IssueService::class, Mockery::mock(IssueService::class));

    $this->artisan('issue:create', [
        '--project' => 'PROJ',
        '--type' => 'Bug',
        '--summary' => 'Fix login',
        '--description-file' => '/no/such/file.md',
    ])
        ->expectsOutputToContain('File not found')
        ->assertExitCode(1);
});
