<?php

namespace App\Services;

class ProjectService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function list(): array
    {
        return $this->jira->get($this->jira->restApi('project'));
    }
}
