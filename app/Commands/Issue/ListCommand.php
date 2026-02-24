<?php

namespace App\Commands\Issue;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Issue;
use App\Services\IssueService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'issue:list
        {--project= : Project key}
        {--type= : Filter by issue type (Bug, Story, Task, etc.)}
        {--status= : Filter by status}
        {--priority= : Filter by priority}
        {--assignee= : Filter by assignee}
        {--reporter= : Filter by reporter}
        {--label= : Filter by label}
        {--jql= : Custom JQL query (overrides other filters)}
        {--limit=20 : Max results}';

    protected $description = 'List or search issues using JQL';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $jql = $this->option('jql');

            if ($jql === null) {
                $conditions = [];

                if ($project = $this->option('project')) {
                    $conditions[] = "project = \"{$project}\"";
                }
                if ($type = $this->option('type')) {
                    $conditions[] = "issuetype = \"{$type}\"";
                }
                if ($status = $this->option('status')) {
                    $conditions[] = "status = \"{$status}\"";
                }
                if ($priority = $this->option('priority')) {
                    $conditions[] = "priority = \"{$priority}\"";
                }
                if ($assignee = $this->option('assignee')) {
                    $conditions[] = $assignee === 'me'
                        ? 'assignee = currentUser()'
                        : "assignee = \"{$assignee}\"";
                }
                if ($reporter = $this->option('reporter')) {
                    $conditions[] = $reporter === 'me'
                        ? 'reporter = currentUser()'
                        : "reporter = \"{$reporter}\"";
                }
                if ($label = $this->option('label')) {
                    $conditions[] = "labels = \"{$label}\"";
                }

                $jql = implode(' AND ', $conditions) ?: 'ORDER BY created DESC';

                if (! empty($conditions)) {
                    $jql .= ' ORDER BY created DESC';
                }
            }

            $response = $issueService->search($jql, maxResults: (int) $this->option('limit'));
            $issues = array_map(
                fn (array $data) => Issue::fromApi($data),
                $response['issues'] ?? []
            );

            $rows = array_map(fn (Issue $issue) => [
                $issue->key,
                $this->colorize($issue->type, $this->stateColor($issue->type)),
                $this->colorize($issue->status, $this->stateColor($issue->status)),
                $issue->priority ?? '-',
                mb_substr($issue->summary, 0, 50),
                $issue->assignee ?? 'Unassigned',
                $this->formatDate($issue->updated),
            ], $issues);

            $this->renderTable(['Key', 'Type', 'Status', 'Priority', 'Summary', 'Assignee', 'Updated'], $rows);

            return self::SUCCESS;
        });
    }
}
