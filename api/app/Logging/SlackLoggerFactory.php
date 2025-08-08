<?php

namespace App\Logging;

use Monolog\Logger;

class SlackLoggerFactory
{
    /**
     * Create a custom Slack logger instance.
     */
    public function __invoke(array $config): Logger
    {
        $handler = new SlackLogHandler(
            $config['token'] ?? null,
            $config['channel'] ?? null
        );

        return new Logger('slack', [$handler]);
    }
}
