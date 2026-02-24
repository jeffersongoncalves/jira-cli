<?php

namespace App\Enums;

enum SprintState: string
{
    case Future = 'future';
    case Active = 'active';
    case Closed = 'closed';

    public function color(): string
    {
        return match ($this) {
            self::Future => 'blue',
            self::Active => 'green',
            self::Closed => 'gray',
        };
    }
}
