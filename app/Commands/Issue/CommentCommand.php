<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\AdfConverter;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\textarea;

class CommentCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:comment
        {key : Issue key (e.g. PROJ-123)}
        {--body= : Comment body}
        {--file= : Read comment body from a markdown file (supports #/##/### headings and - bullet lists)}
        {--marker= : Idempotency marker; re-running with the same marker edits the existing comment instead of creating a new one (requires --file)}';

    protected $description = 'Add a comment to an issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $key = $this->argument('key');
            $file = $this->option('file');
            $marker = $this->option('marker');

            if ($marker && ! $file) {
                $this->components->error('--marker requires --file.');

                return self::FAILURE;
            }

            if ($file) {
                if (! is_file($file)) {
                    $this->components->error("File not found: {$file}");

                    return self::FAILURE;
                }

                $content = AdfConverter::fromMarkdown(file_get_contents($file));

                if ($marker) {
                    $updated = $issueService->findCommentIdByMarker($key, $marker) !== null;
                    $issueService->upsertComment($key, $content, $marker);
                    $this->components->info(($updated ? 'Comment updated on ' : 'Comment added to ').$key.'.');
                } else {
                    $issueService->addRichComment($key, $content);
                    $this->components->info("Comment added to {$key}.");
                }
            } else {
                $body = $this->option('body') ?: textarea(label: 'Comment', required: true);

                $issueService->addComment($key, $body);
                $this->components->info("Comment added to {$key}.");
            }

            return self::SUCCESS;
        });
    }
}
