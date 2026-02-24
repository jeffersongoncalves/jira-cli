<?php

use App\Services\AuthService;

beforeEach(function () {
    $this->authService = new AuthService;
    $this->tempDir = sys_get_temp_dir().'/jira-cli-test-'.uniqid();
});

afterEach(function () {
    $configPath = $this->tempDir.'/config.json';
    if (file_exists($configPath)) {
        unlink($configPath);
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
});

it('returns null when no config exists', function () {
    expect($this->authService->load())->toBeNull();
    expect($this->authService->isAuthenticated())->toBeFalse();
});

it('returns config path', function () {
    $path = $this->authService->getConfigPath();
    expect($path)->toContain('.jira-cli')
        ->and($path)->toContain('config.json');
});
