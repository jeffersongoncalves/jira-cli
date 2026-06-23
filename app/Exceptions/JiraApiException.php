<?php

namespace App\Exceptions;

use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;

class JiraApiException extends ApiException
{
    /**
     * Pull the human-readable message out of a decoded Jira error body.
     *
     * Jira reports errors under "errorMessages" (list) and "errors" (map),
     * falling back to a plain "message" key for transport-level failures.
     *
     * @param  array<string, mixed>  $body
     */
    protected static function extractMessage(array $body): string
    {
        $messages = is_array($body['errorMessages'] ?? null) ? $body['errorMessages'] : [];
        $errors = is_array($body['errors'] ?? null) ? array_values($body['errors']) : [];

        $combined = array_merge(
            array_map('strval', $messages),
            array_map('strval', $errors),
        );

        if ($combined !== []) {
            return implode('; ', $combined);
        }

        if (isset($body['message']) && is_string($body['message'])) {
            return $body['message'];
        }

        return '';
    }
}
