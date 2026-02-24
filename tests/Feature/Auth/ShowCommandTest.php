<?php

use App\DTOs\Credentials;
use App\Enums\AuthType;
use App\Services\AuthService;

it('shows saved credentials', function () {
    $credentials = new Credentials(
        server: 'https://example.atlassian.net',
        username: 'user@example.com',
        apiToken: 'test-token-12345',
        authType: AuthType::Basic,
        project: 'PROJ',
        boardId: 1,
    );

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn($credentials);
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->expectsOutputToContain('example.atlassian.net')
        ->expectsOutputToContain('user@example.com')
        ->assertExitCode(0);
});

it('shows error when no credentials found', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn(null);
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->expectsOutputToContain('No credentials found')
        ->assertExitCode(1);
});
