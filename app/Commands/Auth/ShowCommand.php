<?php

namespace App\Commands\Auth;

use App\Services\AuthService;
use Illuminate\Console\Command;

class ShowCommand extends Command
{
    protected $signature = 'auth:show';

    protected $description = 'Show saved Jira credentials';

    public function handle(AuthService $authService): int
    {
        $credentials = $authService->load();

        if ($credentials === null) {
            $this->components->error('No credentials found. Run "jira auth:save" first.');

            return self::FAILURE;
        }

        $maskedToken = str_repeat('*', max(0, strlen($credentials->apiToken) - 4))
            .substr($credentials->apiToken, -4);

        $this->table(['Key', 'Value'], [
            ['Server', $credentials->server],
            ['Username', $credentials->username],
            ['API Token', $maskedToken],
            ['Auth Type', $credentials->authType->value],
            ['Default Project', $credentials->project ?? '-'],
            ['Default Board ID', $credentials->boardId !== null ? (string) $credentials->boardId : '-'],
        ]);

        return self::SUCCESS;
    }
}
