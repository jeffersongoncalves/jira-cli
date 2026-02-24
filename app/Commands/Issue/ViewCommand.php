<?php

namespace App\Commands\Issue;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Comment;
use App\DTOs\Issue;
use App\Services\IssueService;
use Illuminate\Console\Command;

class ViewCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'issue:view
        {key : Issue key (e.g. PROJ-123)}
        {--comments : Show comments}';

    protected $description = 'View issue details';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $response = $issueService->get($this->argument('key'));
            $issue = Issue::fromApi($response);

            $this->newLine();
            $this->line("  <fg=white;options=bold>{$issue->key}</> {$issue->summary}");
            $this->newLine();
            $this->table(['Field', 'Value'], [
                ['Type', $issue->type],
                ['Status', $this->colorize($issue->status, $this->stateColor($issue->status))],
                ['Priority', $issue->priority ?? '-'],
                ['Assignee', $issue->assignee ?? 'Unassigned'],
                ['Reporter', $issue->reporter ?? '-'],
                ['Labels', implode(', ', $issue->labels) ?: '-'],
                ['Components', implode(', ', $issue->components) ?: '-'],
                ['Created', $this->formatDate($issue->created)],
                ['Updated', $this->formatDate($issue->updated)],
            ]);

            if ($issue->description) {
                $this->newLine();
                $this->components->info('Description');
                $this->line($issue->description);
            }

            if ($this->option('comments')) {
                $commentsResponse = $issueService->getComments($this->argument('key'));
                $comments = array_map(
                    fn (array $data) => Comment::fromApi($data),
                    $commentsResponse['comments'] ?? []
                );

                if (! empty($comments)) {
                    $this->newLine();
                    $this->components->info('Comments');
                    foreach ($comments as $comment) {
                        $this->line("  <fg=cyan>{$comment->authorDisplayName}</> - {$this->formatDate($comment->created)}");
                        $this->line("  {$comment->body}");
                        $this->newLine();
                    }
                }
            }

            return self::SUCCESS;
        });
    }
}
