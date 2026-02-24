<?php

namespace App\DTOs;

class Epic
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $summary,
        public readonly string $status,
        public readonly bool $done = false,
    ) {}

    public static function fromApi(array $data): self
    {
        $fields = $data['fields'] ?? [];

        return new self(
            key: $data['key'],
            name: $fields['customfield_10011'] ?? $fields['summary'] ?? '',
            summary: $fields['summary'] ?? '',
            status: $fields['status']['name'] ?? 'Unknown',
            done: ($fields['resolution'] ?? null) !== null,
        );
    }
}
