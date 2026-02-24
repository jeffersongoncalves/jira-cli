<?php

namespace App\DTOs;

use App\Enums\AuthType;

class Credentials
{
    public function __construct(
        public readonly string $server,
        public readonly string $username,
        public readonly string $apiToken,
        public readonly AuthType $authType = AuthType::Basic,
        public readonly ?string $project = null,
        public readonly ?int $boardId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            server: $data['server'],
            username: $data['username'],
            apiToken: $data['api_token'],
            authType: AuthType::from($data['auth_type'] ?? 'basic'),
            project: $data['project'] ?? null,
            boardId: isset($data['board_id']) ? (int) $data['board_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'server' => $this->server,
            'username' => $this->username,
            'api_token' => $this->apiToken,
            'auth_type' => $this->authType->value,
            'project' => $this->project,
            'board_id' => $this->boardId,
        ];
    }
}
