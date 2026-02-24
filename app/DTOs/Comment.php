<?php

namespace App\DTOs;

class Comment
{
    public function __construct(
        public readonly string $id,
        public readonly string $authorDisplayName,
        public readonly string $body,
        public readonly ?string $created = null,
        public readonly ?string $updated = null,
    ) {}

    public static function fromApi(array $data): self
    {
        $body = $data['body'] ?? '';

        if (is_array($body)) {
            $body = self::extractTextFromAdf($body);
        }

        return new self(
            id: (string) $data['id'],
            authorDisplayName: $data['author']['displayName'] ?? 'Unknown',
            body: $body,
            created: $data['created'] ?? null,
            updated: $data['updated'] ?? null,
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
            if (($child['type'] ?? '') === 'paragraph') {
                $text .= "\n";
            }
        }

        return $text;
    }
}
