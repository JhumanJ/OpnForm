<?php

namespace App\Logging;

use JoliCode\Slack\Api\Client;
use JoliCode\Slack\ClientFactory;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Monolog\Level;
use Exception;

class SlackLogHandler extends AbstractProcessingHandler
{
    protected Client $client;
    protected ?string $channel;
    protected string $username;
    protected string $emoji;

    public function __construct(
        ?string $token,
        ?string $channel = null,
        string $username = 'OpenForm Bot',
        string $emoji = ':robot_face:',
        int|string|Level $level = Level::Debug,
        bool $bubble = true
    ) {
        if (!$token) {
            throw new \InvalidArgumentException('Slack token is required');
        }

        $this->client = ClientFactory::create($token);
        $this->channel = $channel;
        $this->username = $username;
        $this->emoji = $emoji;

        parent::__construct($level, $bubble);
    }

    /**
     * Write the record down to the log of the implementing handler
     */
    protected function write(LogRecord $record): void
    {
        try {
            $blocks = $this->buildSlackBlocks($record);

            $this->client->chatPostMessage([
                'channel' => $this->channel,
                'username' => $this->username,
                'icon_emoji' => $this->emoji,
                'blocks' => $blocks,
                'text' => $record->message, // Fallback for notifications
            ]);
        } catch (Exception $e) {
            // Log the error without causing recursion
            error_log("Failed to send Slack message: " . $e->getMessage());
        }
    }

    /**
     * Build Slack blocks from log record
     */
    protected function buildSlackBlocks(LogRecord $record): array
    {
        $blocks = [];

        // Header block with log level and timestamp
        $blocks[] = [
            'type' => 'header',
            'text' => [
                'type' => 'plain_text',
                'text' => $this->getLevelEmoji($record->level) . ' ' . $record->level->getName() . ' Log',
            ],
        ];

        // Main message block
        $messageText = $this->formatMessage($record);
        $blocks[] = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => $messageText,
            ],
        ];

        // Context block with timestamp and channel info
        $contextElements = [
            [
                'type' => 'mrkdwn',
                'text' => '*Time:* ' . $record->datetime->format('Y-m-d H:i:s T'),
            ],
        ];

        if (!empty($record->context['channel'])) {
            $contextElements[] = [
                'type' => 'mrkdwn',
                'text' => '*Channel:* ' . $record->context['channel'],
            ];
        }

        $blocks[] = [
            'type' => 'context',
            'elements' => $contextElements,
        ];

        // Add action buttons if provided in context
        if (!empty($record->context['actions']) && is_array($record->context['actions'])) {
            $elements = [];
            foreach ($record->context['actions'] as $label => $url) {
                $elements[] = [
                    'type' => 'button',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => $label,
                    ],
                    'url' => $url,
                    'action_id' => 'button_' . strtolower(str_replace(' ', '_', $label)),
                ];
            }

            if (!empty($elements)) {
                $blocks[] = [
                    'type' => 'actions',
                    'elements' => $elements,
                ];
            }
        }

        // Add additional context as fields if present
        if (!empty($record->context) && $this->hasNonSpecialContextKeys($record->context)) {
            $fields = [];
            foreach ($record->context as $key => $value) {
                // Skip special keys that we handle separately
                if (in_array($key, ['actions', 'channel'])) {
                    continue;
                }

                $fields[] = [
                    'type' => 'mrkdwn',
                    'text' => "*{$key}:* " . $this->formatContextValue($value),
                ];
            }

            if (!empty($fields)) {
                $blocks[] = [
                    'type' => 'section',
                    'fields' => $fields,
                ];
            }
        }

        // Add exception details if present
        if (!empty($record->context['exception']) || !empty($record->extra['exception'])) {
            $exception = $record->context['exception'] ?? $record->extra['exception'];
            if ($exception instanceof Exception) {
                $blocks[] = [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => "*Exception:* `" . get_class($exception) . "`\n" .
                            "*File:* `" . $exception->getFile() . ":" . $exception->getLine() . "`\n" .
                            "*Message:* " . $exception->getMessage(),
                    ],
                ];
            }
        }

        return $blocks;
    }

    /**
     * Format the main log message with interpolation
     */
    protected function formatMessage(LogRecord $record): string
    {
        $message = $record->message;

        // Perform simple string interpolation
        if (!empty($record->context)) {
            foreach ($record->context as $key => $value) {
                // Skip special keys and complex objects
                if (in_array($key, ['actions', 'exception']) || is_object($value) || is_array($value)) {
                    continue;
                }

                $message = str_replace('{' . $key . '}', (string) $value, $message);
            }
        }

        return $message;
    }

    /**
     * Get emoji for log level
     */
    protected function getLevelEmoji(Level $level): string
    {
        return match ($level->value) {
            Level::Emergency->value, Level::Alert->value => '🚨',
            Level::Critical->value => '💥',
            Level::Error->value => '❌',
            Level::Warning->value => '⚠️',
            Level::Notice->value => '📢',
            Level::Info->value => 'ℹ️',
            Level::Debug->value => '🐛',
            default => '📝',
        };
    }

    /**
     * Format context value for display
     */
    protected function formatContextValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }

        if (is_object($value)) {
            return get_class($value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /**
     * Check if context has keys other than special ones
     */
    protected function hasNonSpecialContextKeys(array $context): bool
    {
        $specialKeys = ['actions', 'channel', 'exception'];
        $contextKeys = array_keys($context);

        return !empty(array_diff($contextKeys, $specialKeys));
    }
}
