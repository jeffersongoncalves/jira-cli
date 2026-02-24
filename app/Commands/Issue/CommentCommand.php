<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\textarea;

class CommentCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:comment
        {key : Issue key (e.g. PROJ-123)}
        {--body= : Comment body}';

    protected $description = 'Add a comment to an issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $body = $this->option('body') ?: textarea(label: 'Comment', required: true);

            $issueService->addComment($this->argument('key'), $body);

            $this->components->info("Comment added to {$this->argument('key')}.");

            return self::SUCCESS;
        });
    }
}
