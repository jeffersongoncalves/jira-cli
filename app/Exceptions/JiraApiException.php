<?php

namespace App\Exceptions;

use RuntimeException;

class JiraApiException extends RuntimeException
{
    public function __construct(
        string $message = 'Jira API error.',
        int $code = 0,
        public readonly ?array $response = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function fromResponse(int $statusCode, array $body): self
    {
        $messages = $body['errorMessages'] ?? [];
        $errors = $body['errors'] ?? [];

        $message = implode('; ', array_merge($messages, array_values($errors)));

        if ($message === '') {
            $message = "HTTP {$statusCode}";
        }

        return new self("Jira API error: {$message}", $statusCode, $body);
    }
}
