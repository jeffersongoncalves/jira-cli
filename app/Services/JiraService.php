<?php

namespace App\Services;

use App\DTOs\Credentials;
use App\Enums\AuthType;
use App\Exceptions\AuthenticationException;
use App\Exceptions\JiraApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class JiraService
{
    private Client $client;

    private Credentials $credentials;

    public function __construct(AuthService $authService)
    {
        $credentials = $authService->load();

        if ($credentials === null) {
            throw new AuthenticationException;
        }

        $this->credentials = $credentials;
        $this->client = new Client([
            'base_uri' => rtrim($credentials->server, '/'),
            'headers' => $this->buildHeaders($credentials),
            'timeout' => 30,
        ]);
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    public function paginate(string $endpoint, array $query = [], string $itemsKey = 'values'): array
    {
        $allItems = [];
        $startAt = 0;
        $maxResults = $query['maxResults'] ?? 50;

        do {
            $query['startAt'] = $startAt;
            $query['maxResults'] = $maxResults;

            $response = $this->get($endpoint, $query);
            $items = $response[$itemsKey] ?? $response['issues'] ?? [];
            $allItems = array_merge($allItems, $items);

            $total = $response['total'] ?? count($items);
            $startAt += $maxResults;
        } while ($startAt < $total && count($items) === $maxResults);

        return $allItems;
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

    private function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            $body = $response->getBody()->getContents();

            if ($body === '') {
                return [];
            }

            return json_decode($body, true) ?? [];
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = json_decode($e->getResponse()->getBody()->getContents(), true) ?? [];

            if ($statusCode === 401 || $statusCode === 403) {
                throw new AuthenticationException("Authentication failed (HTTP {$statusCode}). Check your credentials.");
            }

            throw JiraApiException::fromResponse($statusCode, $body);
        } catch (GuzzleException $e) {
            throw new JiraApiException("HTTP error: {$e->getMessage()}", $e->getCode());
        }
    }

    private function buildHeaders(Credentials $credentials): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($credentials->authType === AuthType::Bearer) {
            $headers['Authorization'] = "Bearer {$credentials->apiToken}";
        } else {
            $headers['Authorization'] = 'Basic '.base64_encode("{$credentials->username}:{$credentials->apiToken}");
        }

        return $headers;
    }
}
