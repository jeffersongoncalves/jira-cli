<?php

namespace App\DTOs;

class Board
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $type,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            type: $data['type'] ?? 'Unknown',
        );
    }
}
