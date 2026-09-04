<?php

namespace App\Concerns;

use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;

trait InteractsWithJira
{
    use HandlesApiErrors;

    protected function handleJiraErrors(callable $callback): int
    {
        return $this->handleApiErrors($callback);
    }

    /**
     * Read text from a file path, or from stdin when $path is "-".
     * Returns false if $path is not "-" and the file doesn't exist.
     */
    protected function readTextSource(string $path): string|false
    {
        if ($path === '-') {
            return file_get_contents('php://stdin');
        }

        return is_file($path) ? file_get_contents($path) : false;
    }
}
