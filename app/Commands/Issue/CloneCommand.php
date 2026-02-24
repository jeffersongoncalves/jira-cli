<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

class CloneCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:clone
        {key : Issue key to clone (e.g. PROJ-123)}';

    protected $description = 'Clone (duplicate) an issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $response = $issueService->cloneIssue($this->argument('key'));

            $this->components->info("Issue cloned: {$response['key']} (from {$this->argument('key')})");

            return self::SUCCESS;
        });
    }
}
