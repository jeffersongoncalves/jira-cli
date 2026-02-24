<?php

namespace App\Commands\Sprint;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Sprint;
use App\Services\SprintService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'sprint:list
        {--board= : Board ID}
        {--state= : Filter by state (active, closed, future)}';

    protected $description = 'List sprints for a board';

    public function handle(SprintService $sprintService): int
    {
        return $this->handleJiraErrors(function () use ($sprintService) {
            $boardId = $this->option('board');

            if (! $boardId) {
                $this->components->error('Board ID is required. Use --board=ID');

                return self::FAILURE;
            }

            $response = $sprintService->list((int) $boardId, $this->option('state'));
            $sprints = array_map(
                fn (array $data) => Sprint::fromApi($data),
                $response['values'] ?? []
            );

            $rows = array_map(fn (Sprint $sprint) => [
                $sprint->id,
                $sprint->name,
                $this->colorize($sprint->state->value, $sprint->state->color()),
                $this->formatDate($sprint->startDate),
                $this->formatDate($sprint->endDate),
                mb_substr($sprint->goal ?? '-', 0, 30),
            ], $sprints);

            $this->renderTable(['ID', 'Name', 'State', 'Start', 'End', 'Goal'], $rows);

            return self::SUCCESS;
        });
    }
}
