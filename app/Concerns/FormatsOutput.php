<?php

namespace App\Concerns;

use JeffersonGoncalves\LaravelZero\Console\FormatsOutput as BaseFormatsOutput;

trait FormatsOutput
{
    use BaseFormatsOutput;

    /**
     * Resolve a console color for a given Jira state.
     *
     * Keeps the original 'gray' fallback for unknown states.
     */
    protected function stateColor(string $state): string
    {
        return $this->stateColors()[strtoupper($state)] ?? 'gray';
    }

    /**
     * Jira-specific state -> color map.
     *
     * @return array<string, string>
     */
    protected function stateColors(): array
    {
        return [
            'TO DO' => 'blue',
            'OPEN' => 'blue',
            'NEW' => 'blue',
            'FUTURE' => 'blue',
            'IN PROGRESS' => 'yellow',
            'ACTIVE' => 'yellow',
            'BUILDING' => 'yellow',
            'DONE' => 'green',
            'CLOSED' => 'green',
            'RESOLVED' => 'green',
            'RELEASED' => 'green',
            'DECLINED' => 'red',
            'REJECTED' => 'red',
            'BLOCKED' => 'red',
        ];
    }
}
