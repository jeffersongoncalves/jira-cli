<?php

namespace App\Commands\Epic;

use App\Concerns\InteractsWithJira;
use App\Services\EpicService;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class CreateCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'epic:create
        {--project= : Project key}
        {--summary= : Epic summary}
        {--description= : Epic description}';

    protected $description = 'Create a new epic';

    public function handle(EpicService $epicService): int
    {
        return $this->handleJiraErrors(function () use ($epicService) {
            $project = $this->option('project') ?: text(label: 'Project key', required: true);
            $summary = $this->option('summary') ?: text(label: 'Epic summary', required: true);
            $description = $this->option('description') ?: textarea(label: 'Description (optional)');

            $response = $epicService->create($project, $summary, $description ?: null);

            $this->components->info("Epic created: {$response['key']}");

            return self::SUCCESS;
        });
    }
}
