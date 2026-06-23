<?php

namespace App\Services;

use App\DTOs\Credentials;
use JeffersonGoncalves\LaravelZero\Credentials\AbstractAuthService;
use JeffersonGoncalves\LaravelZero\Credentials\CredentialsContract;

class AuthService extends AbstractAuthService
{
    public function load(): ?Credentials
    {
        $credentials = parent::load();

        return $credentials instanceof Credentials ? $credentials : null;
    }

    protected function appName(): string
    {
        return 'jira-cli';
    }

    protected function fromArray(array $data): CredentialsContract
    {
        return Credentials::fromArray($data);
    }
}
