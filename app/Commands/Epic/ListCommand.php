<?php

namespace App\Commands\Epic;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Epic;
use App\Services\EpicService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'epic:list
        {--project= : Project key}';

    protected $description = 'List epics in a project';

    public function handle(EpicService $epicService): int
    {
        return $this->handleJiraErrors(function () use ($epicService) {
            $project = $this->option('project');

            if (! $project) {
                $this->components->error('Project key is required. Use --project=KEY');

                return self::FAILURE;
            }

            $response = $epicService->list($project);
            $epics = array_map(
                fn (array $data) => Epic::fromApi($data),
                $response['issues'] ?? []
            );

            $rows = array_map(fn (Epic $epic) => [
                $epic->key,
                mb_substr($epic->name, 0, 50),
                $this->colorize($epic->status, $this->stateColor($epic->status)),
                $epic->done ? '<fg=green>Yes</>' : 'No',
            ], $epics);

            $this->renderTable(['Key', 'Name', 'Status', 'Done'], $rows);

            return self::SUCCESS;
        });
    }
}
