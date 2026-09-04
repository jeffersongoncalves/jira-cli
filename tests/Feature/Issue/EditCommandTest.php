<?php

use App\Services\IssueService;

it('edits an issue', function () {
    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('edit')
        ->with('PROJ-1', Mockery::type('array'))
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:edit PROJ-1 --summary="Updated summary"')
        ->expectsOutputToContain('PROJ-1 updated')
        ->assertExitCode(0);
});

it('shows warning when no fields provided', function () {
    $issueService = Mockery::mock(IssueService::class);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:edit PROJ-1')
        ->expectsOutputToContain('No fields to update')
        ->assertExitCode(1);
});

it('edits description from --description-file', function () {
    $file = tempnam(sys_get_temp_dir(), 'desc');
    file_put_contents($file, "### Heading\n\nBody text.");

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('edit')
        ->with('PROJ-1', Mockery::on(fn (array $fields) => $fields['description']['type'] === 'doc'))
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:edit', ['key' => 'PROJ-1', '--description-file' => $file])
        ->expectsOutputToContain('PROJ-1 updated')
        ->assertExitCode(0);

    unlink($file);
});

it('errors when --description-file does not exist', function () {
    $this->app->instance(IssueService::class, Mockery::mock(IssueService::class));

    $this->artisan('issue:edit', ['key' => 'PROJ-1', '--description-file' => '/no/such/file.md'])
        ->expectsOutputToContain('File not found')
        ->assertExitCode(1);
});
