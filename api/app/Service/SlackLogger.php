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
}
