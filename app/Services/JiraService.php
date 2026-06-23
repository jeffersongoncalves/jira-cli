<?php

namespace App\Services;

use App\DTOs\Credentials;
use App\Enums\AuthType;
use App\Exceptions\AuthenticationException;
use App\Exceptions\JiraApiException;
use JeffersonGoncalves\LaravelZero\ApiClient\AbstractApiClient;
use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;
use JeffersonGoncalves\LaravelZero\ApiClient\Auth;

class JiraService extends AbstractApiClient
{
    private Credentials $credentials;

    public function __construct(AuthService $authService)
    {
        $credentials = $authService->load();

        if ($credentials === null) {
            throw new AuthenticationException;
        }

        $this->credentials = $credentials;

        $auth = $credentials->authType === AuthType::Bearer
            ? Auth::bearer($credentials->apiToken)
            : Auth::basic($credentials->username, $credentials->apiToken);

        parent::__construct(rtrim($credentials->server, '/'), $auth);
    }

    public function restApi(string $path): string
    {
        return config('jira.rest_api_path').$path;
    }

    public function agileApi(string $path): string
    {
        return config('jira.agile_api_path').$path;
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    protected function newApiException(int $statusCode, array $body): ApiException
    {
        if ($statusCode === 401 || $statusCode === 403) {
            throw new AuthenticationException("Authentication failed (HTTP {$statusCode}). Check your credentials.");
        }

        return JiraApiException::fromResponse($statusCode, $body);
    }
}
