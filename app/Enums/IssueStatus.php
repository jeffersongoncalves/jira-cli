<?php

namespace App\Enums;

enum IssueStatus: string
{
    case ToDo = 'To Do';
    case InProgress = 'In Progress';
    case Done = 'Done';

    public function color(): string
    {
        return match ($this) {
            self::ToDo => 'blue',
            self::InProgress => 'yellow',
            self::Done => 'green',
        };
    }
}
