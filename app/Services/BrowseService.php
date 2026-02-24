<?php

namespace App\Services;

class BrowseService
{
    public function open(string $url): bool
    {
        $command = match (PHP_OS_FAMILY) {
            'Windows' => "start \"\" \"{$url}\"",
            'Darwin' => "open \"{$url}\"",
            default => "xdg-open \"{$url}\"",
        };

        exec($command, $output, $exitCode);

        return $exitCode === 0;
    }
}
