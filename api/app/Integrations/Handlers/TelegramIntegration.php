<?php

namespace App\Integrations\Handlers;

use App\Models\Forms\Form;
use App\Open\MentionParser;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Arr;
use Vinkla\Hashids\Facades\Hashids;

class TelegramIntegration extends AbstractIntegrationHandler
{
    protected const TELEGRAM_API_BASE = 'https://api.telegram.org/bot';

    public static function getValidationRules(?Form $form): array
    {
        return [
            'include_submission_data' => 'boolean',
            'include_hidden_fields_submission_data' => ['nullable', 'boolean'],
            'link_open_form' => 'boolean',
            'link_edit_form' => 'boolean',
            'views_submissions_count' => 'boolean',
            'link_edit_submission' => 'boolean'
        ];
    }

    protected function getChatId(): ?string
    {
        return $this->provider->provider_user_id;
    }

    protected function getWebhookUrl(): ?string
    {
        $token = config('services.telegram.bot_token');
        if (!$token) {
            return null;
        }
        return self::TELEGRAM_API_BASE . $token . '/sendMessage';
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl()) && $this->formIntegration->oauth_id && $this->form->is_pro && parent::shouldRun();
    }

    protected function getWebhookData(): array
    {
        $settings = (array) $this->integrationData ?? [];

        $formatter = (new FormSubmissionFormatter($this->form, $this->submissionData))->outputStringsOnly();
        if (Arr::get($settings, 'include_hidden_fields_submission_data', false)) {
            $formatter->showHiddenFields();
        }
        $formattedData = $formatter->getFieldsWithValue();

        $message = Arr::get($settings, 'message', 'New form submission');
        $messageText = (new MentionParser($message, $formattedData))->parse();

        if (Arr::get($settings, 'include_submission_data', true)) {
            $messageText .= "\n\n📝 *Submission Details:*\n";
            foreach ($formattedData as $field) {
                $tmpVal = is_array($field['value']) ? implode(',', $field['value']) : $field['value'];
                $messageText .= "*" . ucfirst($field['name']) . "*: " . $tmpVal . "\n";
            }
        }

        if (Arr::get($settings, 'views_submissions_count', true)) {
            $messageText .= "\n📊 *Form Statistics:*\n";
            $messageText .= "👀 *Views*: " . (string) $this->form->views_count . "\n";
            $messageText .= "🖊️ *Submissions*: " . (string) $this->form->submissions_count . "\n";
        }

        // Add links section
        $links = [];
        if (Arr::get($settings, 'link_open_form', true)) {
            $links[] = "[🔗 Open Form](" . $this->form->share_url . ")";
        }
        if (Arr::get($settings, 'link_edit_form', true)) {
            $editFormURL = front_url('forms/' . $this->form->slug . '/show');
            $links[] = "[✍️ Edit Form](" . $editFormURL . ")";
        }
        if (Arr::get($settings, 'link_edit_submission', true) && $this->form->editable_submissions) {
            $submissionId = Hashids::encode($this->submissionData['submission_id']);
            $links[] = "[✍️ " . $this->form->editable_submissions_button_text . "](" . $this->form->share_url . "?submission_id=" . $submissionId . ")";
        }

        if (count($links) > 0) {
            $messageText .= "\n" . implode(" • ", $links);
        }

        return [
            'chat_id' => $this->getChatId(),
            'text' => $this->escapeMarkdownV2($messageText),
            'parse_mode' => 'MarkdownV2'
        ];
    }

    /**
     * Escape special characters for Telegram MarkdownV2 format
     * @see https://core.telegram.org/bots/api#markdownv2-style
     */
    protected function escapeMarkdownV2(string $text): string
    {
        $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        return str_replace($specialChars, array_map(fn ($char) => '\\' . $char, $specialChars), $text);
    }
}
