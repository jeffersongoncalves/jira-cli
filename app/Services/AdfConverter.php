<?php

namespace App\Services;

class AdfConverter
{
    /**
     * Convert simple markdown (headings, bullet lists, paragraphs) to ADF content nodes.
     */
    public static function fromMarkdown(string $markdown): array
    {
        $lines = preg_split('/\R/', trim($markdown)) ?: [];
        $nodes = [];
        /** @var list<string> $paragraphLines */
        $paragraphLines = [];
        /** @var list<string> $bulletItems */
        $bulletItems = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                [$nodes, $paragraphLines] = self::flushParagraph($nodes, $paragraphLines);
                [$nodes, $bulletItems] = self::flushBullets($nodes, $bulletItems);

                continue;
            }

            if (preg_match('/^(#{1,3})\s+(.+)$/', $trimmed, $m) === 1) {
                [$nodes, $paragraphLines] = self::flushParagraph($nodes, $paragraphLines);
                [$nodes, $bulletItems] = self::flushBullets($nodes, $bulletItems);
                $nodes[] = self::heading($m[2], strlen($m[1]));

                continue;
            }

            if (preg_match('/^[-*]\s+(.+)$/', $trimmed, $m) === 1) {
                [$nodes, $paragraphLines] = self::flushParagraph($nodes, $paragraphLines);
                $bulletItems[] = $m[1];

                continue;
            }

            [$nodes, $bulletItems] = self::flushBullets($nodes, $bulletItems);
            $paragraphLines[] = $trimmed;
        }

        [$nodes, $paragraphLines] = self::flushParagraph($nodes, $paragraphLines);
        [$nodes] = self::flushBullets($nodes, $bulletItems);

        return $nodes;
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     * @param  list<string>  $paragraphLines
     * @return array{0: list<array<string, mixed>>, 1: list<string>}
     */
    private static function flushParagraph(array $nodes, array $paragraphLines): array
    {
        if ($paragraphLines !== []) {
            $nodes[] = self::paragraph(implode(' ', $paragraphLines));
            $paragraphLines = [];
        }

        return [$nodes, $paragraphLines];
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     * @param  list<string>  $bulletItems
     * @return array{0: list<array<string, mixed>>, 1: list<string>}
     */
    private static function flushBullets(array $nodes, array $bulletItems): array
    {
        if ($bulletItems !== []) {
            $nodes[] = self::bulletList($bulletItems);
            $bulletItems = [];
        }

        return [$nodes, $bulletItems];
    }

    public static function paragraph(string $text): array
    {
        return ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $text]]];
    }

    public static function heading(string $text, int $level = 3): array
    {
        return ['type' => 'heading', 'attrs' => ['level' => $level], 'content' => [['type' => 'text', 'text' => $text]]];
    }

    public static function bulletList(array $items): array
    {
        return [
            'type' => 'bulletList',
            'content' => array_map(
                static fn (string $item): array => ['type' => 'listItem', 'content' => [self::paragraph($item)]],
                $items
            ),
        ];
    }

    public static function doc(array $content): array
    {
        return ['type' => 'doc', 'version' => 1, 'content' => $content];
    }
}
