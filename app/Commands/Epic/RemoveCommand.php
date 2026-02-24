<?php

namespace App\Commands\Epic;

use App\Concerns\InteractsWithJira;
use App\Services\EpicService;
use Illuminate\Console\Command;

class RemoveCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'epic:remove
        {issues* : Issue keys to remove from their epic}';

    protected $description = 'Remove issues from their epic';

    public function handle(EpicService $epicService): int
    {
        return $this->handleJiraErrors(function () use ($epicService) {
            $issues = $this->argument('issues');

            $epicService->removeIssues($issues);

            $this->components->info('Removed '.count($issues).' issue(s) from their epic.');

            return self::SUCCESS;
        });
    }
}
