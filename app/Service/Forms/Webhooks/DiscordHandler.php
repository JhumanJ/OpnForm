<?php

namespace App\Service\Forms\Webhooks;

use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Str;

class DiscordHandler extends AbstractWebhookHandler
{

    protected function getProviderName(): string
    {
        return 'Discord';
    }

    protected function getWebhookUrl(): ?string
    {
        return $this->form->discord_webhook_url;
    }

    protected function getWebhookData(): array
    {
        $submissionString = "";
        $formatter = (new FormSubmissionFormatter($this->form, $this->data))->outputStringsOnly();

        foreach ($formatter->getFieldsWithValue() as $field) {
            $tmpVal = is_array($field['value']) ? implode(",", $field['value']) : $field['value'];
            $submissionString .= "**" . ucfirst($field['name']) . "**: `" . $tmpVal . "`\n";
        }

        $form_name = $this->form->title;
        $formURL = url("forms/" . $this->form->slug . "/show/submissions");

        return [
            "content" => "@here We have received a new submission for **$form_name**",
            "username" => config('app.name'),
            "avatar_url" => asset('img/logo.png'),
            "tts" => false,
            "embeds" => [
                [
                    "title" => "🔗 Go to $form_name",

                    "type" => "rich",

                    "description" => $submissionString,

                    "url" => $formURL,

                    "color" => hexdec(str_replace('#', '', $this->form->color)),

                    "footer" => [
                        "text" => config('app.name'),
                        "icon_url" => asset('img/logo.png'),
                    ],

                    "author" => [
                        "name" => config('app.name'),
                        "url" => config('app.url'),
                    ],

                    "fields" => [
                        [
                            "name" => "Views 👀",
                            "value" => (string)$this->form->views_count,
                            "inline" => true
                        ],
                        [
                            "name" => "Submissions 🖊️",
                            "value" => (string)$this->form->submissions_count,
                            "inline" => true
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl())
            && str_contains($this->getWebhookUrl(), 'https://discord.com/api/webhooks')
            && $this->form->is_pro;
    }
}
