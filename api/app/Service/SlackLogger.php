<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;

class SlackLogger
{
    /**
     * Log security events to the security Slack channel
     */
    public static function security(string $message, array $context = []): void
    {
        Log::channel('slack_security')->info($message, $context);
    }

    /**
     * Log error events to the error Slack channel
     */
    public static function error(string $message, array $context = []): void
    {
        Log::channel('slack_error')->error($message, $context);
    }
}
