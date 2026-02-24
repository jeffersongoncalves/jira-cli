<?php

namespace App\DTOs;

use App\Enums\SprintState;

class Sprint
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly SprintState $state,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate = null,
        public readonly ?string $goal = null,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            state: SprintState::from($data['state']),
            startDate: $data['startDate'] ?? null,
            endDate: $data['endDate'] ?? null,
            goal: $data['goal'] ?? null,
        );
    }
}
