<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class DeleteCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:delete
        {key : Issue key (e.g. PROJ-123)}
        {--force : Skip confirmation}';

    protected $description = 'Delete an issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $issueKey = $this->argument('key');

            if (! $this->option('force')) {
                $confirmed = confirm(
                    label: "Are you sure you want to delete {$issueKey}?",
                    default: false,
                );

                if (! $confirmed) {
                    $this->components->info('Cancelled.');

                    return self::SUCCESS;
                }
            }

            $issueService->delete($issueKey);

            $this->components->info("Issue {$issueKey} deleted.");

            return self::SUCCESS;
        });
    }
}
