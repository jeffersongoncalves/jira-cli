<?php

namespace App\Services;

use App\DTOs\Credentials;
use App\Enums\AuthType;

class AuthService
{
    private ?Credentials $credentials = null;

    public function save(
        string $server,
        string $username,
        string $apiToken,
        AuthType $authType = AuthType::Basic,
        ?string $project = null,
        ?int $boardId = null,
    ): void {
        $credentials = new Credentials(
            server: rtrim($server, '/'),
            username: $username,
            apiToken: $apiToken,
            authType: $authType,
            project: $project,
            boardId: $boardId,
        );

        $configDir = $this->getConfigDir();

        if (! is_dir($configDir)) {
            mkdir($configDir, 0700, true);
        }

        $configPath = $this->getConfigPath();
        file_put_contents($configPath, json_encode($credentials->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        chmod($configPath, 0600);

        $this->credentials = $credentials;
    }

    public function load(): ?Credentials
    {
        if ($this->credentials !== null) {
            return $this->credentials;
        }

        $configPath = $this->getConfigPath();

        if (! file_exists($configPath)) {
            return null;
        }

        $data = json_decode(file_get_contents($configPath), true);

        if (! is_array($data) || ! isset($data['server'], $data['username'], $data['api_token'])) {
            return null;
        }

        $this->credentials = Credentials::fromArray($data);

        return $this->credentials;
    }

    public function isAuthenticated(): bool
    {
        return $this->load() !== null;
    }

    public function getConfigDir(): string
    {
        $home = $this->getHomeDir();

        return $home.DIRECTORY_SEPARATOR.'.jira-cli';
    }

    public function getConfigPath(): string
    {
        return $this->getConfigDir().DIRECTORY_SEPARATOR.'config.json';
    }

    private function getHomeDir(): string
    {
        return match (true) {
            isset($_SERVER['HOME']) => $_SERVER['HOME'],
            isset($_SERVER['USERPROFILE']) => $_SERVER['USERPROFILE'],
            isset($_SERVER['HOMEDRIVE'], $_SERVER['HOMEPATH']) => $_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH'],
            default => '~',
        };
    }
}
