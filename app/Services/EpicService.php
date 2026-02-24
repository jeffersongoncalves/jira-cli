<?php

namespace App\Services;

class EpicService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function list(string $project): array
    {
        return $this->jira->get($this->jira->restApi('search'), [
            'jql' => "project = \"{$project}\" AND issuetype = Epic ORDER BY created DESC",
            'fields' => 'summary,status,customfield_10011,resolution',
        ]);
    }

    public function create(string $project, string $summary, ?string $description = null): array
    {
        $fields = [
            'project' => ['key' => $project],
            'summary' => $summary,
            'issuetype' => ['name' => 'Epic'],
        ];

        if ($description !== null) {
            $fields['description'] = [
                'type' => 'doc',
                'version' => 1,
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => $description],
                        ],
                    ],
                ],
            ];
        }

        return $this->jira->post($this->jira->restApi('issue'), ['fields' => $fields]);
    }

    public function getIssues(string $epicKey): array
    {
        return $this->jira->get($this->jira->agileApi("epic/{$epicKey}/issue"));
    }

    public function addIssues(string $epicKey, array $issueKeys): array
    {
        return $this->jira->post($this->jira->agileApi("epic/{$epicKey}/issue"), [
            'issues' => $issueKeys,
        ]);
    }

    public function removeIssues(array $issueKeys): array
    {
        return $this->jira->post($this->jira->agileApi('epic/none/issue'), [
            'issues' => $issueKeys,
        ]);
    }
}
