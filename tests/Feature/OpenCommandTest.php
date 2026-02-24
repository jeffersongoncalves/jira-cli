<?php

use App\DTOs\Credentials;
use App\Enums\AuthType;
use App\Services\AuthService;
use App\Services\BrowseService;

it('opens issue in browser', function () {
    $credentials = new Credentials(
        server: 'https://example.atlassian.net',
        username: 'user@example.com',
        apiToken: 'test-token',
        authType: AuthType::Basic,
    );

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn($credentials);
    $this->app->instance(AuthService::class, $authService);

    $browseService = Mockery::mock(BrowseService::class);
    $browseService->shouldReceive('open')
        ->with('https://example.atlassian.net/browse/PROJ-1')
        ->once()
        ->andReturn(true);
    $this->app->instance(BrowseService::class, $browseService);

    $this->artisan('open PROJ-1')
        ->expectsOutputToContain('Opening')
        ->assertExitCode(0);
});

it('shows error when not authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn(null);
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('open PROJ-1')
        ->expectsOutputToContain('Not authenticated')
        ->assertExitCode(1);
});
