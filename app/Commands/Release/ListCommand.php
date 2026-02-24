<?php

namespace App\Commands\Release;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithJira;
use App\DTOs\Release;
use App\Services\ReleaseService;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithJira;

    protected $signature = 'release:list
        {--project= : Project key}';

    protected $description = 'List project releases/versions';

    public function handle(ReleaseService $releaseService): int
    {
        return $this->handleJiraErrors(function () use ($releaseService) {
            $project = $this->option('project');

            if (! $project) {
                $this->components->error('Project key is required. Use --project=KEY');

                return self::FAILURE;
            }

            $response = $releaseService->list($project);

            $releases = array_map(
                fn (array $data) => Release::fromApi($data),
                $response
            );

            $rows = array_map(fn (Release $release) => [
                $release->id,
                $release->name,
                $release->released ? '<fg=green>Released</>' : '<fg=yellow>Unreleased</>',
                $release->releaseDate ?? '-',
                mb_substr($release->description ?? '-', 0, 40),
            ], $releases);

            $this->renderTable(['ID', 'Name', 'Status', 'Date', 'Description'], $rows);

            return self::SUCCESS;
        });
    }
}
