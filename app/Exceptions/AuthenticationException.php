<?php

namespace App\Exceptions;

use RuntimeException;

class AuthenticationException extends RuntimeException
{
    public function __construct(string $message = 'Not authenticated. Run "jira auth:save" first.')
    {
        parent::__construct($message);
    }
}
