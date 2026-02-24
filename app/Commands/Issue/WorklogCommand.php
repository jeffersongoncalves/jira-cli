<?php

namespace App\Commands\Issue;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Worklog;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class WorklogCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'issue:worklog
        {key : Issue key (e.g. PROJ-123)}
        {--time= : Time spent (e.g. 2h, 30m, 1d)}
        {--comment= : Worklog comment}
        {--list : List existing worklogs instead of adding}';

    protected $description = 'Add a worklog entry or list worklogs';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $issueKey = $this->argument('key');

            if ($this->option('list')) {
                return $this->listWorklogs($issueService, $issueKey);
            }

            $timeSpent = $this->option('time') ?: text(label: 'Time spent (e.g. 2h, 30m, 1d)', required: true);
            $comment = $this->option('comment');

            $issueService->addWorklog($issueKey, $timeSpent, $comment);

            $this->components->info("Worklog added to {$issueKey}: {$timeSpent}");

            return self::SUCCESS;
        });
    }

    private function listWorklogs(IssueService $issueService, string $issueKey): int
    {
        $response = $issueService->getWorklogs($issueKey);
        $worklogs = array_map(
            fn (array $data) => Worklog::fromApi($data),
            $response['worklogs'] ?? []
        );

        $rows = array_map(fn (Worklog $wl) => [
            $wl->id,
            $wl->authorDisplayName,
            $wl->timeSpent,
            $this->formatDate($wl->started),
            mb_substr($wl->comment ?? '-', 0, 40),
        ], $worklogs);

        $this->renderTable(['ID', 'Author', 'Time Spent', 'Started', 'Comment'], $rows);

        return self::SUCCESS;
    }
}
