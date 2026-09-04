<?php

namespace App\Services;

use App\DTOs\Comment;

class IssueService
{
    public function __construct(
        private readonly JiraService $jira,
    ) {}

    public function search(string $jql, array $fields = [], int $maxResults = 50): array
    {
        $query = ['jql' => $jql, 'maxResults' => $maxResults];

        if (! empty($fields)) {
            $query['fields'] = implode(',', $fields);
        }

        // /rest/api/3/search was removed by Atlassian; /search/jql is the replacement (CHANGE-2046).
        return $this->jira->get($this->jira->restApi('search/jql'), $query);
    }

    public function get(string $issueKey, array $fields = []): array
    {
        $query = [];

        if (! empty($fields)) {
            $query['fields'] = implode(',', $fields);
        }

        return $this->jira->get($this->jira->restApi("issue/{$issueKey}"), $query);
    }

    public function create(array $fields): array
    {
        return $this->jira->post($this->jira->restApi('issue'), ['fields' => $fields]);
    }

    public function edit(string $issueKey, array $fields): array
    {
        return $this->jira->put($this->jira->restApi("issue/{$issueKey}"), ['fields' => $fields]);
    }

    public function delete(string $issueKey): array
    {
        return $this->jira->delete($this->jira->restApi("issue/{$issueKey}"));
    }

    public function assign(string $issueKey, ?string $accountId): array
    {
        return $this->jira->put($this->jira->restApi("issue/{$issueKey}/assignee"), [
            'accountId' => $accountId,
        ]);
    }

    public function getTransitions(string $issueKey): array
    {
        return $this->jira->get($this->jira->restApi("issue/{$issueKey}/transitions"));
    }

    public function transition(string $issueKey, string $transitionId): array
    {
        return $this->jira->post($this->jira->restApi("issue/{$issueKey}/transitions"), [
            'transition' => ['id' => $transitionId],
        ]);
    }

    public function addComment(string $issueKey, string $body): array
    {
        return $this->jira->post($this->jira->restApi("issue/{$issueKey}/comment"), [
            'body' => [
                'type' => 'doc',
                'version' => 1,
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => $body],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function getComments(string $issueKey): array
    {
        return $this->jira->get($this->jira->restApi("issue/{$issueKey}/comment"));
    }

    public function addRichComment(string $issueKey, array $adfContent): array
    {
        return $this->jira->post($this->jira->restApi("issue/{$issueKey}/comment"), [
            'body' => AdfConverter::doc($adfContent),
        ]);
    }

    public function updateComment(string $issueKey, string $commentId, array $adfContent): array
    {
        return $this->jira->put($this->jira->restApi("issue/{$issueKey}/comment/{$commentId}"), [
            'body' => AdfConverter::doc($adfContent),
        ]);
    }

    public function findCommentIdByMarker(string $issueKey, string $marker): ?string
    {
        $comments = $this->getComments($issueKey)['comments'] ?? [];

        foreach ($comments as $data) {
            if (str_contains(Comment::fromApi($data)->body, $marker)) {
                return (string) $data['id'];
            }
        }

        return null;
    }

    /**
     * Create or update (by marker) a rich comment, so re-running with the same marker
     * edits the existing comment instead of piling up duplicates.
     */
    public function upsertComment(string $issueKey, array $adfContent, string $marker): array
    {
        $content = array_merge([AdfConverter::paragraph($marker)], $adfContent);

        $existingId = $this->findCommentIdByMarker($issueKey, $marker);

        return $existingId !== null
            ? $this->updateComment($issueKey, $existingId, $content)
            : $this->addRichComment($issueKey, $content);
    }

    public function addWorklog(string $issueKey, string $timeSpent, ?string $comment = null): array
    {
        $data = ['timeSpent' => $timeSpent];

        if ($comment !== null) {
            $data['comment'] = [
                'type' => 'doc',
                'version' => 1,
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => $comment],
                        ],
                    ],
                ],
            ];
        }

        return $this->jira->post($this->jira->restApi("issue/{$issueKey}/worklog"), $data);
    }

    public function getWorklogs(string $issueKey): array
    {
        return $this->jira->get($this->jira->restApi("issue/{$issueKey}/worklog"));
    }

    public function link(string $type, string $inwardIssue, string $outwardIssue): array
    {
        return $this->jira->post($this->jira->restApi('issueLink'), [
            'type' => ['name' => $type],
            'inwardIssue' => ['key' => $inwardIssue],
            'outwardIssue' => ['key' => $outwardIssue],
        ]);
    }

    public function unlink(string $linkId): array
    {
        return $this->jira->delete($this->jira->restApi("issueLink/{$linkId}"));
    }

    public function getLinkTypes(): array
    {
        return $this->jira->get($this->jira->restApi('issueLinkType'));
    }

    public function cloneIssue(string $issueKey): array
    {
        $original = $this->get($issueKey);
        $fields = $original['fields'] ?? [];

        $newFields = [
            'project' => ['key' => $fields['project']['key'] ?? ''],
            'summary' => '[Clone] '.$fields['summary'],
            'issuetype' => ['id' => (string) ($fields['issuetype']['id'] ?? '')],
        ];

        if (isset($fields['priority']['id'])) {
            $newFields['priority'] = ['id' => $fields['priority']['id']];
        }

        if (isset($fields['labels'])) {
            $newFields['labels'] = $fields['labels'];
        }

        if (isset($fields['description'])) {
            $newFields['description'] = $fields['description'];
        }

        return $this->create($newFields);
    }

    public function watch(string $issueKey, string $accountId): array
    {
        // @phpstan-ignore argument.type (Jira's add-watcher endpoint takes the raw accountId payload)
        return $this->jira->post($this->jira->restApi("issue/{$issueKey}/watchers"), [$accountId]);
    }

    public function searchUsers(string $query): array
    {
        return $this->jira->get($this->jira->restApi('user/search'), ['query' => $query]);
    }
}
