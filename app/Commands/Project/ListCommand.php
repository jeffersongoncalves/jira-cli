<?php

namespace App\Commands\Project;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Project;
use App\Services\ProjectService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'project:list';

    protected $description = 'List accessible projects';

    public function handle(ProjectService $projectService): int
    {
        return $this->handleJiraErrors(function () use ($projectService) {
            $response = $projectService->list();

            $projects = array_map(
                fn (array $data) => Project::fromApi($data),
                $response
            );

            $rows = array_map(fn (Project $project) => [
                $project->key,
                $project->name,
                $project->lead ?? '-',
                $project->projectTypeKey ?? '-',
            ], $projects);

            $this->renderTable(['Key', 'Name', 'Lead', 'Type'], $rows);

            return self::SUCCESS;
        });
    }
}
