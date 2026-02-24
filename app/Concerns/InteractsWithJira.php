<?php

namespace App\Concerns;

use App\Exceptions\AuthenticationException;
use App\Exceptions\JiraApiException;

trait InteractsWithJira
{
    protected function handleJiraErrors(callable $callback): int
    {
        try {
            return $callback();
        } catch (AuthenticationException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        } catch (JiraApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
