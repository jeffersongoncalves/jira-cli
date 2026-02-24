<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use App\Services\JiraService;
use Illuminate\Console\Command;

class AssignCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:assign
        {key : Issue key (e.g. PROJ-123)}
        {user? : Assignee (use "me" for yourself, or account ID, empty to unassign)}';

    protected $description = 'Assign issue to a user';

    public function handle(IssueService $issueService, JiraService $jiraService): int
    {
        return $this->handleJiraErrors(function () use ($issueService, $jiraService) {
            $issueKey = $this->argument('key');
            $user = $this->argument('user');

            if ($user === null || $user === '') {
                $issueService->assign($issueKey, null);
                $this->components->info("Issue {$issueKey} unassigned.");

                return self::SUCCESS;
            }

            if ($user === 'me') {
                $myself = $jiraService->get($jiraService->restApi('myself'));
                $user = $myself['accountId'];
            }

            $issueService->assign($issueKey, $user);
            $this->components->info("Issue {$issueKey} assigned.");

            return self::SUCCESS;
        });
    }
}
