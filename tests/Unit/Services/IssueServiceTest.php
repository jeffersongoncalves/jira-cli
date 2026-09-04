<?php

use App\Services\IssueService;
use App\Services\JiraService;

it('searches issues via the search/jql endpoint', function () {
    $jira = Mockery::mock(JiraService::class);
    $jira->shouldReceive('restApi')->with('search/jql')->andReturn('search/jql');
    $jira->shouldReceive('get')
        ->with('search/jql', ['jql' => 'project = PROJ', 'maxResults' => 20, 'fields' => 'summary,status'])
        ->once()
        ->andReturn(['issues' => []]);

    $service = new IssueService($jira);

    $service->search('project = PROJ', fields: ['summary', 'status'], maxResults: 20);
});

it('finds a comment id by marker in existing comments', function () {
    $jira = Mockery::mock(JiraService::class);
    $jira->shouldReceive('restApi')->with('issue/PROJ-1/comment')->andReturn('issue/PROJ-1/comment');
    $jira->shouldReceive('get')->with('issue/PROJ-1/comment')->andReturn(loadFixture('comments'));

    $service = new IssueService($jira);

    expect($service->findCommentIdByMarker('PROJ-1', 'Working on this now'))->toBe('10001')
        ->and($service->findCommentIdByMarker('PROJ-1', 'no such marker'))->toBeNull();
});

it('creates a new comment via upsertComment when no marker match exists', function () {
    $jira = Mockery::mock(JiraService::class);
    $jira->shouldReceive('restApi')->with('issue/PROJ-1/comment')->andReturn('issue/PROJ-1/comment');
    $jira->shouldReceive('get')->with('issue/PROJ-1/comment')->andReturn(['comments' => []]);
    $jira->shouldReceive('post')
        ->with('issue/PROJ-1/comment', Mockery::on(fn (array $payload) => str_contains(
            $payload['body']['content'][0]['content'][0]['text'],
            '<!-- marker -->'
        )))
        ->once()
        ->andReturn(['id' => '99']);

    $service = new IssueService($jira);

    $service->upsertComment('PROJ-1', [], '<!-- marker -->');
});

it('updates the existing comment via upsertComment when a marker match exists', function () {
    $jira = Mockery::mock(JiraService::class);
    $jira->shouldReceive('restApi')->with('issue/PROJ-1/comment')->andReturn('issue/PROJ-1/comment');
    $jira->shouldReceive('restApi')->with('issue/PROJ-1/comment/10001')->andReturn('issue/PROJ-1/comment/10001');
    $jira->shouldReceive('get')->with('issue/PROJ-1/comment')->andReturn(loadFixture('comments'));
    $jira->shouldReceive('put')->with('issue/PROJ-1/comment/10001', Mockery::type('array'))
        ->once()
        ->andReturn(['id' => '10001']);

    $service = new IssueService($jira);

    $service->upsertComment('PROJ-1', [], 'Working on this now');
});
