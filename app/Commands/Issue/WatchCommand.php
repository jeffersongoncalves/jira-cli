<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use App\Services\JiraService;
use Illuminate\Console\Command;

class WatchCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:watch
        {key : Issue key (e.g. PROJ-123)}';

    protected $description = 'Watch an issue (add yourself as watcher)';

    public function handle(IssueService $issueService, JiraService $jiraService): int
    {
        return $this->handleJiraErrors(function () use ($issueService, $jiraService) {
            $myself = $jiraService->get($jiraService->restApi('myself'));
            $issueService->watch($this->argument('key'), $myself['accountId']);

            $this->components->info("Now watching {$this->argument('key')}.");

            return self::SUCCESS;
        });
    }
}
