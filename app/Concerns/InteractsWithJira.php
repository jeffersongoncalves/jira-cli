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
}
