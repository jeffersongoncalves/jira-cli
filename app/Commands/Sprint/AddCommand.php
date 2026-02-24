<?php

namespace App\Commands\Sprint;

use App\Concerns\InteractsWithJira;
use App\Services\SprintService;
use Illuminate\Console\Command;

class AddCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'sprint:add
        {sprint : Sprint ID}
        {issues* : Issue keys to add}';

    protected $description = 'Add issues to a sprint';

    public function handle(SprintService $sprintService): int
    {
        return $this->handleJiraErrors(function () use ($sprintService) {
            $sprintId = (int) $this->argument('sprint');
            $issues = $this->argument('issues');

            $sprintService->addIssues($sprintId, $issues);

            $this->components->info('Added '.count($issues)." issue(s) to sprint {$sprintId}.");

            return self::SUCCESS;
        });
    }
}
