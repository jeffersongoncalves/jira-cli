<?php

use App\Services\AuthService;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fallbackWhen(true);
});

it('saves credentials successfully', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once();
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.jira-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save')
        ->expectsQuestion('Jira server URL (e.g. https://your-domain.atlassian.net)', 'https://example.atlassian.net')
        ->expectsQuestion('Authentication type', 'basic')
        ->expectsQuestion('Email address', 'user@example.com')
        ->expectsQuestion('API Token', 'test-token')
        ->expectsQuestion('Default project key (optional)', 'PROJ')
        ->expectsQuestion('Default board ID (optional)', '1')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});
