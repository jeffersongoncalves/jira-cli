<?php

namespace App\Commands\Sprint;

use App\Concerns\InteractsWithJira;
use App\Services\SprintService;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

class CloseCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'sprint:close
        {sprint : Sprint ID}
        {--force : Skip confirmation}';

    protected $description = 'Close/complete a sprint';

    public function handle(SprintService $sprintService): int
    {
        return $this->handleJiraErrors(function () use ($sprintService) {
            $sprintId = (int) $this->argument('sprint');

            if (! $this->option('force')) {
                $confirmed = confirm(
                    label: "Are you sure you want to close sprint {$sprintId}?",
                    default: false,
                );

                if (! $confirmed) {
                    $this->components->info('Cancelled.');

                    return self::SUCCESS;
                }
            }

            $sprintService->close($sprintId);

            $this->components->info("Sprint {$sprintId} closed.");

            return self::SUCCESS;
        });
    }
}
