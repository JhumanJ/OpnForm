<?php

namespace App\Service;

use Illuminate\Support\Facades\Log;

class SlackLogger
{
    /**
     * Log to the general Slack channel
     */
    public static function general(string $level, string $message, array $context = []): void
    {
        Log::channel('slack_general')->log($level, $message, $context);
    }

    /**
     * Log security events to the security Slack channel
     */
    public static function security(string $message, array $context = []): void
    {
        Log::channel('slack_security')->warning($message, $context);
    }
}
