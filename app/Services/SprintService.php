<?php

namespace App\Services;

class SprintService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function list(int $boardId, ?string $state = null): array
    {
        $query = [];

        if ($state !== null) {
            $query['state'] = $state;
        }

        return $this->jira->get($this->jira->agileApi("board/{$boardId}/sprint"), $query);
    }

    public function getIssues(int $sprintId): array
    {
        return $this->jira->get($this->jira->agileApi("sprint/{$sprintId}/issue"));
    }

    public function addIssues(int $sprintId, array $issueKeys): array
    {
        return $this->jira->post($this->jira->agileApi("sprint/{$sprintId}/issue"), [
            'issues' => $issueKeys,
        ]);
    }

    public function close(int $sprintId): array
    {
        return $this->jira->post($this->jira->agileApi("sprint/{$sprintId}"), [
            'state' => 'closed',
        ]);
    }
}
