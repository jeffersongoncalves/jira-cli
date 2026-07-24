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

it('adds a rich comment from --file', function () {
    $file = tempnam(sys_get_temp_dir(), 'comment');
    file_put_contents($file, "### Heading\n\nSome body text.\n\n- item one\n- item two");

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('addRichComment')
        ->with('PROJ-1', Mockery::type('array'))
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:comment', ['key' => 'PROJ-1', '--file' => $file])
        ->expectsOutputToContain('Comment added')
        ->assertExitCode(0);

    unlink($file);
});

it('creates a new comment via --marker when none matches yet', function () {
    $file = tempnam(sys_get_temp_dir(), 'comment');
    file_put_contents($file, 'Body text.');

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('findCommentIdByMarker')
        ->with('PROJ-1', '<!-- marker -->')
        ->once()
        ->andReturn(null);
    $issueService->shouldReceive('upsertComment')
        ->with('PROJ-1', Mockery::type('array'), '<!-- marker -->')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:comment', ['key' => 'PROJ-1', '--file' => $file, '--marker' => '<!-- marker -->'])
        ->expectsOutputToContain('Comment added')
        ->assertExitCode(0);

    unlink($file);
});

it('updates the existing comment via --marker when one already matches', function () {
    $file = tempnam(sys_get_temp_dir(), 'comment');
    file_put_contents($file, 'Body text.');

    $issueService = Mockery::mock(IssueService::class);
    $issueService->shouldReceive('findCommentIdByMarker')
        ->with('PROJ-1', '<!-- marker -->')
        ->once()
        ->andReturn('10001');
    $issueService->shouldReceive('upsertComment')
        ->with('PROJ-1', Mockery::type('array'), '<!-- marker -->')
        ->once()
        ->andReturn([]);
    $this->app->instance(IssueService::class, $issueService);

    $this->artisan('issue:comment', ['key' => 'PROJ-1', '--file' => $file, '--marker' => '<!-- marker -->'])
        ->expectsOutputToContain('Comment updated')
        ->assertExitCode(0);

    unlink($file);
});

it('rejects --marker without --file', function () {
    $this->app->instance(IssueService::class, Mockery::mock(IssueService::class));

    $this->artisan('issue:comment', ['key' => 'PROJ-1', '--marker' => '<!-- marker -->'])
        ->expectsOutputToContain('--marker requires --file')
        ->assertExitCode(1);
});

it('errors when --file does not exist', function () {
    $this->app->instance(IssueService::class, Mockery::mock(IssueService::class));

    $this->artisan('issue:comment PROJ-1 --file=/no/such/file.md')
        ->expectsOutputToContain('File not found')
        ->assertExitCode(1);
});
