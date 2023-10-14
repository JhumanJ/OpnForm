<?php

namespace App\Service\Forms\Webhooks;

use App\Models\Forms\Form;

class WebhookHandlerProvider
{
    const SLACK_PROVIDER = 'slack';
    const DISCORD_PROVIDER = 'discord';
    const SIMPLE_WEBHOOK_PROVIDER = 'webhook';
    const ZAPIER_PROVIDER = 'zapier';

    public static function getProvider(Form $form, array $data, string $provider, ?string $webhookUrl = null)
    {
        switch ($provider) {
            case self::SLACK_PROVIDER:
                return new SlackHandler($form, $data);
            case self::DISCORD_PROVIDER:
                return new DiscordHandler($form, $data);
            case self::SIMPLE_WEBHOOK_PROVIDER:
                return new SimpleWebhookHandler($form, $data);
            case self::ZAPIER_PROVIDER:
                if (is_null($webhookUrl)) {
                    throw new \Exception('Zapier webhook url is required');
                }
                return new ZapierHandler($form, $data, $webhookUrl);
            default:
                throw new \Exception('Unknown webhook provider');
        }
    }
}
