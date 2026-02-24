<?php

namespace App\Concerns;

use Carbon\Carbon;

trait FormatsOutput
{
    protected function renderTable(array $headers, array $rows): void
    {
        if (empty($rows)) {
            $this->components->info('No results found.');

            return;
        }

        $this->table($headers, $rows);
    }

    protected function stateColor(string $state): string
    {
        return match (strtolower($state)) {
            'to do', 'open', 'new', 'future' => 'blue',
            'in progress', 'active', 'building' => 'yellow',
            'done', 'closed', 'resolved', 'released' => 'green',
            'declined', 'rejected', 'blocked' => 'red',
            default => 'gray',
        };
    }

    protected function colorize(string $text, string $color): string
    {
        return "<fg={$color}>{$text}</>";
    }

    protected function formatDate(?string $dateString): string
    {
        if ($dateString === null || $dateString === '') {
            return '-';
        }

        try {
            return Carbon::parse($dateString)->format('Y-m-d H:i');
        } catch (\Exception) {
            return $dateString;
        }
    }
}
