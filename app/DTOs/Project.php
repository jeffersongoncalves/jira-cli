<?php

namespace App\DTOs;

class Project
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly ?string $lead = null,
        public readonly ?string $projectTypeKey = null,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            key: $data['key'],
            name: $data['name'],
            lead: $data['lead']['displayName'] ?? null,
            projectTypeKey: $data['projectTypeKey'] ?? null,
        );
    }
}
