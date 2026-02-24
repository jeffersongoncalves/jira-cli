<?php

namespace App\Commands;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\Services\JiraService;
use Illuminate\Console\Command;

class MeCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'me';

    protected $description = 'Show current authenticated user';

    public function handle(JiraService $jiraService): int
    {
        return $this->handleJiraErrors(function () use ($jiraService) {
            $user = $jiraService->get($jiraService->restApi('myself'));

            $this->table(['Field', 'Value'], [
                ['Account ID', $user['accountId'] ?? '-'],
                ['Display Name', $user['displayName'] ?? '-'],
                ['Email', $user['emailAddress'] ?? '-'],
                ['Active', ($user['active'] ?? false) ? 'Yes' : 'No'],
                ['Timezone', $user['timeZone'] ?? '-'],
            ]);

            return self::SUCCESS;
        });
    }
}
