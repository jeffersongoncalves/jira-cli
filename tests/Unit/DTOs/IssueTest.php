<?php

use App\DTOs\Issue;

it('creates issue from API response', function () {
    $data = loadFixture('issue-single');
    $issue = Issue::fromApi($data);

    expect($issue->key)->toBe('PROJ-1')
        ->and($issue->summary)->toBe('Fix login bug')
        ->and($issue->type)->toBe('Bug')
        ->and($issue->status)->toBe('In Progress')
        ->and($issue->priority)->toBe('High')
        ->and($issue->assignee)->toBe('John Doe')
        ->and($issue->reporter)->toBe('Jane Smith')
        ->and($issue->labels)->toBe(['backend', 'urgent'])
        ->and($issue->components)->toBe(['Auth']);
});

it('extracts text from ADF description', function () {
    $data = loadFixture('issue-single');
    $issue = Issue::fromApi($data);

    expect($issue->description)->toContain('Users cannot login');
});

it('handles null assignee', function () {
    $data = loadFixture('issues-search');
    $issues = $data['issues'];
    $issue = Issue::fromApi($issues[1]);

    expect($issue->assignee)->toBeNull();
});
