<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\AdfConverter;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class CreateCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:create
        {--project= : Project key}
        {--type= : Issue type (Bug, Story, Task, etc.)}
        {--summary= : Issue summary}
        {--description= : Issue description (use - to read from stdin)}
        {--description-file= : Read description from a markdown file (- for stdin); supports #/##/### headings and - bullet lists}
        {--priority= : Issue priority}
        {--assignee= : Assignee account ID}
        {--label=* : Labels}
        {--component=* : Components}';

    protected $description = 'Create a new issue';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $project = $this->option('project') ?: text(label: 'Project key', required: true);
            $type = $this->option('type') ?: select(
                label: 'Issue type',
                options: ['Bug', 'Story', 'Task', 'Sub-task'],
                default: 'Task',
            );
            $summary = $this->option('summary') ?: text(label: 'Summary', required: true);

            $fields = [
                'project' => ['key' => $project],
                'issuetype' => ['name' => $type],
                'summary' => $summary,
            ];

            if ($descriptionFile = $this->option('description-file')) {
                $markdown = $this->readTextSource($descriptionFile);
                if ($markdown === false) {
                    $this->components->error("File not found: {$descriptionFile}");

                    return self::FAILURE;
                }

                $fields['description'] = AdfConverter::doc(AdfConverter::fromMarkdown($markdown));
            } else {
                $description = $this->option('description');
                $description = $description === '-' ? $this->readTextSource('-') : $description;
                $description = $description ?: textarea(label: 'Description (optional)');

                if ($description) {
                    $fields['description'] = AdfConverter::doc([AdfConverter::paragraph($description)]);
                }
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

            $response = $issueService->create($fields);

            $this->components->info("Issue created: {$response['key']}");

            return self::SUCCESS;
        });
    }
}
