<?php

namespace App\DTOs;

class Issue
{
    public function __construct(
        public readonly string $key,
        public readonly string $summary,
        public readonly string $type,
        public readonly string $status,
        public readonly ?string $priority = null,
        public readonly ?string $assignee = null,
        public readonly ?string $reporter = null,
        public readonly ?string $created = null,
        public readonly ?string $updated = null,
        public readonly ?string $description = null,
        public readonly array $labels = [],
        public readonly array $components = [],
    ) {}

    public static function fromApi(array $data): self
    {
        $fields = $data['fields'] ?? [];

        return new self(
            key: $data['key'],
            summary: $fields['summary'] ?? '',
            type: $fields['issuetype']['name'] ?? 'Unknown',
            status: $fields['status']['name'] ?? 'Unknown',
            priority: $fields['priority']['name'] ?? null,
            assignee: $fields['assignee']['displayName'] ?? null,
            reporter: $fields['reporter']['displayName'] ?? null,
            created: $fields['created'] ?? null,
            updated: $fields['updated'] ?? null,
            description: self::extractDescription($fields['description'] ?? null),
            labels: $fields['labels'] ?? [],
            components: array_map(fn (array $c) => $c['name'], $fields['components'] ?? []),
        );
    }

    private static function extractDescription(mixed $description): ?string
    {
        if ($description === null) {
            return null;
        }

        if (is_string($description)) {
            return $description;
        }

        if (is_array($description) && isset($description['content'])) {
            return self::extractTextFromAdf($description);
        }

        return null;
    }

    private static function extractTextFromAdf(array $node): string
    {
        $text = '';

        if (isset($node['text'])) {
            return $node['text'];
        }

        foreach ($node['content'] ?? [] as $child) {
            $text .= self::extractTextFromAdf($child);
            if (($child['type'] ?? '') === 'paragraph') {
                $text .= "\n";
            }
        }

        return $text;
    }
}
