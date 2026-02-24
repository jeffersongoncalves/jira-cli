<?php

namespace App\Services;

class ReleaseService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function list(string $project): array
    {
        return $this->jira->get($this->jira->restApi("project/{$project}/versions"));
    }
}
