<?php

use App\DTOs\Credentials;
use App\Enums\AuthType;

it('creates credentials from array', function () {
    $data = [
        'server' => 'https://example.atlassian.net',
        'username' => 'user@example.com',
        'api_token' => 'test-token',
        'auth_type' => 'basic',
        'project' => 'PROJ',
        'board_id' => 1,
    ];

    $credentials = Credentials::fromArray($data);

    expect($credentials->server)->toBe('https://example.atlassian.net')
        ->and($credentials->username)->toBe('user@example.com')
        ->and($credentials->apiToken)->toBe('test-token')
        ->and($credentials->authType)->toBe(AuthType::Basic)
        ->and($credentials->project)->toBe('PROJ')
        ->and($credentials->boardId)->toBe(1);
});

it('converts credentials to array', function () {
    $credentials = new Credentials(
        server: 'https://example.atlassian.net',
        username: 'user@example.com',
        apiToken: 'test-token',
        authType: AuthType::Bearer,
        project: 'PROJ',
        boardId: 5,
    );

    $array = $credentials->toArray();

    expect($array)->toBe([
        'server' => 'https://example.atlassian.net',
        'username' => 'user@example.com',
        'api_token' => 'test-token',
        'auth_type' => 'bearer',
        'project' => 'PROJ',
        'board_id' => 5,
    ]);
});

it('handles null optional fields', function () {
    $data = [
        'server' => 'https://example.atlassian.net',
        'username' => 'user@example.com',
        'api_token' => 'test-token',
    ];

    $credentials = Credentials::fromArray($data);

    expect($credentials->project)->toBeNull()
        ->and($credentials->boardId)->toBeNull()
        ->and($credentials->authType)->toBe(AuthType::Basic);
});
