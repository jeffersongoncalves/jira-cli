<?php

namespace App\Commands;

use App\Concerns\InteractsWithJira;
use App\Services\JiraService;
use Illuminate\Console\Command;

class ServerInfoCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'serverinfo';

    protected $description = 'Show Jira server information';

    public function handle(JiraService $jiraService): int
    {
        return $this->handleJiraErrors(function () use ($jiraService) {
            $info = $jiraService->get($jiraService->restApi('serverInfo'));

            $this->table(['Field', 'Value'], [
                ['Base URL', $info['baseUrl'] ?? '-'],
                ['Version', $info['version'] ?? '-'],
                ['Build Number', (string) ($info['buildNumber'] ?? '-')],
                ['Build Date', $info['buildDate'] ?? '-'],
                ['Server Title', $info['serverTitle'] ?? '-'],
                ['Deployment Type', $info['deploymentType'] ?? '-'],
            ]);

            return self::SUCCESS;
        });
    }
}
