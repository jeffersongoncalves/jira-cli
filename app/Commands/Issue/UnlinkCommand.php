<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

class UnlinkCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:unlink
        {linkId : Issue link ID}';

    protected $description = 'Remove a link between issues';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $issueService->unlink($this->argument('linkId'));

            $this->components->info("Link {$this->argument('linkId')} removed.");

            return self::SUCCESS;
        });
    }
}
