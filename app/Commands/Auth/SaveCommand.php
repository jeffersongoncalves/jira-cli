<?php

namespace App\Commands\Auth;

use App\DTOs\Credentials;
use App\Enums\AuthType;
use App\Services\AuthService;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SaveCommand extends Command
{
    protected $signature = 'auth:save
        {--server= : Jira server URL (e.g. https://your-domain.atlassian.net)}
        {--auth-type= : Authentication type (basic or bearer)}
        {--username= : Email address (basic) or username (bearer)}
        {--token= : API token or personal access token}
        {--project= : Default project key}
        {--board= : Default board ID}';

    protected $description = 'Save Jira Cloud credentials';

    public function handle(AuthService $authService): int
    {
        $server = $this->option('server') ?: text(
            label: 'Jira server URL (e.g. https://your-domain.atlassian.net)',
            required: true,
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_URL) ? null : 'Please enter a valid URL.',
        );

        $authType = $this->option('auth-type') ?: select(
            label: 'Authentication type',
            options: [
                'basic' => 'Basic (Email + API Token)',
                'bearer' => 'Bearer (Personal Access Token)',
            ],
            default: 'basic',
        );

        if (! in_array($authType, ['basic', 'bearer'], true)) {
            $this->components->error('Invalid --auth-type. Use "basic" or "bearer".');

            return self::FAILURE;
        }

        $username = $this->option('username') ?: text(
            label: $authType === 'basic' ? 'Email address' : 'Username',
            required: true,
        );

        $apiToken = $this->option('token') ?: password(
            label: $authType === 'basic' ? 'API Token' : 'Personal Access Token',
            required: true,
        );

        $project = $this->option('project') ?: text(
            label: 'Default project key (optional)',
        );

        $boardId = $this->option('board') ?: text(
            label: 'Default board ID (optional)',
        );

        $authService->save(new Credentials(
            server: rtrim($server, '/'),
            username: $username,
            apiToken: $apiToken,
            authType: AuthType::from($authType),
            project: $project ?: null,
            boardId: $boardId ? (int) $boardId : null,
        ));

        $this->components->info("Credentials saved to {$authService->getConfigPath()}");

        return self::SUCCESS;
    }
}
