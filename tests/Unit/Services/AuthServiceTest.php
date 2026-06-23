<?php

use App\Services\AuthService;

beforeEach(function () {
    $this->tempDir = sys_get_temp_dir().'/jira-cli-test-'.uniqid();
    $this->originalHome = $_SERVER['HOME'] ?? null;
    $_SERVER['HOME'] = $this->tempDir;
    $this->authService = new AuthService;
});

afterEach(function () {
    $configPath = $this->tempDir.'/.jira-cli/config.json';
    if (file_exists($configPath)) {
        unlink($configPath);
    }
    if (is_dir($this->tempDir.'/.jira-cli')) {
        rmdir($this->tempDir.'/.jira-cli');
    }
    if (is_dir($this->tempDir)) {
        rmdir($this->tempDir);
    }
    if ($this->originalHome === null) {
        unset($_SERVER['HOME']);
    } else {
        $_SERVER['HOME'] = $this->originalHome;
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
