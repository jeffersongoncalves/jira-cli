<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

class EditCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:edit
        {key : Issue key (e.g. PROJ-123)}
        {--summary= : New summary}
        {--priority= : New priority}
        {--assignee= : New assignee account ID}
        {--label=* : Replace labels}
        {--component=* : Replace components}';

    protected $description = 'Edit an existing issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $fields = [];

            if ($summary = $this->option('summary')) {
                $fields['summary'] = $summary;
            }

            if ($priority = $this->option('priority')) {
                $fields['priority'] = ['name' => $priority];
            }

            if ($assignee = $this->option('assignee')) {
                $fields['assignee'] = ['accountId' => $assignee];
            }

            $labels = $this->option('label');
            if (! empty($labels)) {
                $fields['labels'] = $labels;
            }

            $components = $this->option('component');
            if (! empty($components)) {
                $fields['components'] = array_map(fn ($c) => ['name' => $c], $components);
            }

            if (empty($fields)) {
                $this->components->warn('No fields to update. Use options like --summary, --priority, etc.');

                return self::FAILURE;
            }

            $issueService->edit($this->argument('key'), $fields);

            $this->components->info("Issue {$this->argument('key')} updated.");

            return self::SUCCESS;
        });
    }
}
