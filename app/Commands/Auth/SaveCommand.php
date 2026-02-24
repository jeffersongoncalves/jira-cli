<?php

namespace App\Commands\Auth;

use App\Enums\AuthType;
use App\Services\AuthService;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SaveCommand extends Command
{
    protected $signature = 'auth:save';

    protected $description = 'Save Jira Cloud credentials';

    public function handle(AuthService $authService): int
    {
        $server = text(
            label: 'Jira server URL (e.g. https://your-domain.atlassian.net)',
            required: true,
            validate: fn (string $value) => filter_var($value, FILTER_VALIDATE_URL) ? null : 'Please enter a valid URL.',
        );

        $authType = select(
            label: 'Authentication type',
            options: [
                'basic' => 'Basic (Email + API Token)',
                'bearer' => 'Bearer (Personal Access Token)',
            ],
            default: 'basic',
        );

        $username = text(
            label: $authType === 'basic' ? 'Email address' : 'Username',
            required: true,
        );

        $apiToken = password(
            label: $authType === 'basic' ? 'API Token' : 'Personal Access Token',
            required: true,
        );

        $project = text(
            label: 'Default project key (optional)',
        );

        $boardId = text(
            label: 'Default board ID (optional)',
        );

        $authService->save(
            server: $server,
            username: $username,
            apiToken: $apiToken,
            authType: AuthType::from($authType),
            project: $project ?: null,
            boardId: $boardId ? (int) $boardId : null,
        );

        $this->components->info("Credentials saved to {$authService->getConfigPath()}");

        return self::SUCCESS;
    }
}
