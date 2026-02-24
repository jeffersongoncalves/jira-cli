<?php

use App\DTOs\Comment;

it('creates comment from API response', function () {
    $data = loadFixture('comments');
    $comment = Comment::fromApi($data['comments'][0]);

    expect($comment->id)->toBe('10001')
        ->and($comment->authorDisplayName)->toBe('John Doe')
        ->and($comment->body)->toContain('Working on this now');
});
