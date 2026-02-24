<?php

namespace App\Commands;

use App\Concerns\InteractsWithJira;
use App\Services\AuthService;
use App\Services\BrowseService;
use Illuminate\Console\Command;

class OpenCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'open
        {key? : Issue key or project key (e.g. PROJ-123 or PROJ)}';

    protected $description = 'Open issue or project in the browser';

    public function handle(AuthService $authService, BrowseService $browseService): int
    {
        return $this->handleJiraErrors(function () use ($authService, $browseService) {
            $credentials = $authService->load();

            if ($credentials === null) {
                $this->components->error('Not authenticated. Run "jira auth:save" first.');

                return self::FAILURE;
            }

            $key = $this->argument('key');
            $baseUrl = rtrim($credentials->server, '/');

            if ($key !== null) {
                $url = str_contains($key, '-')
                    ? "{$baseUrl}/browse/{$key}"
                    : "{$baseUrl}/jira/software/projects/{$key}/board";
            } else {
                $url = $baseUrl;
            }

            $browseService->open($url);

            $this->components->info("Opening {$url}");

            return self::SUCCESS;
        });
    }
}
