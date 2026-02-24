<?php

namespace App\DTOs;

class Worklog
{
    public function __construct(
        public readonly string $id,
        public readonly string $authorDisplayName,
        public readonly string $timeSpent,
        public readonly ?string $started = null,
        public readonly ?string $comment = null,
    ) {}

    public static function fromApi(array $data): self
    {
        $comment = $data['comment'] ?? null;

        if (is_array($comment)) {
            $comment = self::extractTextFromAdf($comment);
        }

        return new self(
            id: (string) $data['id'],
            authorDisplayName: $data['author']['displayName'] ?? 'Unknown',
            timeSpent: $data['timeSpent'] ?? '0m',
            started: $data['started'] ?? null,
            comment: $comment,
        );
    }

    private static function extractTextFromAdf(array $node): string
    {
        $text = '';

        if (isset($node['text'])) {
            return $node['text'];
        }

        foreach ($node['content'] ?? [] as $child) {
            $text .= self::extractTextFromAdf($child);
        }

        return $text;
    }
}
