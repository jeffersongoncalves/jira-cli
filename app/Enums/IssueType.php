<?php

namespace App\Enums;

enum IssueType: string
{
    case Bug = 'Bug';
    case Story = 'Story';
    case Task = 'Task';
    case Epic = 'Epic';
    case Subtask = 'Sub-task';

    public function color(): string
    {
        return match ($this) {
            self::Bug => 'red',
            self::Story => 'green',
            self::Task => 'blue',
            self::Epic => 'magenta',
            self::Subtask => 'cyan',
        };
    }
}
