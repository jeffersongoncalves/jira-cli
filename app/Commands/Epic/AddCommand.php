<?php

namespace App\Commands\Epic;

use App\Concerns\InteractsWithJira;
use App\Services\EpicService;
use Illuminate\Console\Command;

class AddCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'epic:add
        {epic : Epic key (e.g. PROJ-100)}
        {issues* : Issue keys to add}';

    protected $description = 'Add issues to an epic';

    public function handle(EpicService $epicService): int
    {
        return $this->handleJiraErrors(function () use ($epicService) {
            $epicKey = $this->argument('epic');
            $issues = $this->argument('issues');

            $epicService->addIssues($epicKey, $issues);

            $this->components->info('Added '.count($issues)." issue(s) to epic {$epicKey}.");

            return self::SUCCESS;
        });
    }
}
