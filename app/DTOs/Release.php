<?php

namespace App\DTOs;

class Release
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $released,
        public readonly ?string $releaseDate = null,
        public readonly ?string $description = null,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: $data['name'],
            released: $data['released'] ?? false,
            releaseDate: $data['releaseDate'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
