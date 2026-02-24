<?php

namespace App\Commands\Issue;

use App\Concerns\InteractsWithJira;
use App\Services\IssueService;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class MoveCommand extends Command
{
    use InteractsWithJira;

    protected $signature = 'issue:move
        {key : Issue key (e.g. PROJ-123)}
        {--status= : Target status name}';

    protected $description = 'Transition issue to a new status';

    public function handle(IssueService $issueService): int
    {
        return $this->handleJiraErrors(function () use ($issueService) {
            $issueKey = $this->argument('key');
            $response = $issueService->getTransitions($issueKey);
            $transitions = $response['transitions'] ?? [];

            if (empty($transitions)) {
                $this->components->warn('No available transitions for this issue.');

                return self::FAILURE;
            }

            $options = [];
            foreach ($transitions as $transition) {
                $options[$transition['id']] = $transition['name'];
            }

            $targetStatus = $this->option('status');

            if ($targetStatus !== null) {
                $transitionId = array_search($targetStatus, $options);
                if ($transitionId === false) {
                    $this->components->error("Status \"{$targetStatus}\" not found. Available: ".implode(', ', $options));

                    return self::FAILURE;
                }
            } else {
                $transitionId = select(
                    label: 'Select target status',
                    options: $options,
                );
            }

            $issueService->transition($issueKey, (string) $transitionId);

            $this->components->info("Issue {$issueKey} moved to \"{$options[$transitionId]}\".");

            return self::SUCCESS;
        });
    }
}
