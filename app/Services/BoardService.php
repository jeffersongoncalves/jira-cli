<?php

namespace App\Services;

class BoardService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function list(?string $project = null): array
    {
        $query = [];

        if ($project !== null) {
            $query['projectKeyOrId'] = $project;
        }

        return $this->jira->get($this->jira->agileApi('board'), $query);
    }
}
