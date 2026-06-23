<?php

namespace App\Exceptions;

use JeffersonGoncalves\LaravelZero\Credentials\AuthenticationException as BaseAuthenticationException;

class AuthenticationException extends BaseAuthenticationException
{
    public function __construct(string $message = 'Not authenticated. Run "jira auth:save" first.')
    {
        parent::__construct($message);
    }
}
