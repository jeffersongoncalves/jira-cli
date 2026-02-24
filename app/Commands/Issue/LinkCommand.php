<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class LinkCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:link
        {inward : Inward issue key (e.g. PROJ-123)}
        {outward : Outward issue key (e.g. PROJ-456)}
        {--type= : Link type name}';

    protected $description = 'Link two issues together';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $type = $this->option('type');

            if ($type === null) {
                $response = $issueService->getLinkTypes();
                $linkTypes = $response['issueLinkTypes'] ?? [];

                if (empty($linkTypes)) {
                    $this->components->error('No link types available.');

                    return self::FAILURE;
                }

                $options = [];
                foreach ($linkTypes as $lt) {
                    $options[$lt['name']] = "{$lt['name']} ({$lt['inward']} / {$lt['outward']})";
                }

                $type = select(label: 'Link type', options: $options);
            }

            $issueService->link($type, $this->argument('inward'), $this->argument('outward'));

            $this->components->info("Linked {$this->argument('inward')} -> {$this->argument('outward')} ({$type}).");

            return self::SUCCESS;
        });
    }
}
