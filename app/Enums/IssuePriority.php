<?php

namespace App\Enums;

enum IssuePriority: string
{
    case Highest = 'Highest';
    case High = 'High';
    case Medium = 'Medium';
    case Low = 'Low';
    case Lowest = 'Lowest';

    public function color(): string
    {
        return match ($this) {
            self::Highest => 'red',
            self::High => 'yellow',
            self::Medium => 'white',
            self::Low => 'cyan',
            self::Lowest => 'blue',
        };
    }
}
