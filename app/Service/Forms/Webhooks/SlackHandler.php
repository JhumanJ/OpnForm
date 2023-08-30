<?php

namespace App\Service\Forms\Webhooks;

use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class SlackHandler extends AbstractWebhookHandler
{

    protected function getProviderName(): string
    {
        return 'Slack';
    }

    protected function getWebhookUrl(): ?string
    {
        return $this->form->slack_webhook_url;
    }

    protected function getWebhookData(): array
    {
        $submissionString = '';
        $formatter = (new FormSubmissionFormatter($this->form, $this->data))->outputStringsOnly();
        foreach ($formatter->getFieldsWithValue() as $field) {
            $tmpVal = is_array($field['value']) ? implode(',', $field['value']) : $field['value'];
            $submissionString .= '>*' . ucfirst($field['name']) . '*: ' . $tmpVal . " \n";
        }

        $formURL = url('forms/' . $this->form->slug);
        $editFormURL = url('forms/' . $this->form->slug . '/show');
        $submissionId = Hashids::encode($this->data['submission_id']);
        $externalLinks = [
            '*<' . $formURL . '|🔗 Open Form>*',
            '*<' . $editFormURL . '|✍️ Edit Form>*'
        ];
        if ($this->form->editable_submissions) {
            $externalLinks[] = '*<' . $this->form->share_url . '?submission_id=' . $submissionId . '|✍️ ' . $this->form->editable_submissions_button_text . '>*';
        }

        return [
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => 'New submission for your form *<' . $formURL . '|' . $this->form->title . ':>*',
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $submissionString,
                    ],
                ],
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => implode('     ', $externalLinks),
                    ],
                ],
            ],
        ];
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl())
            && str_contains($this->getWebhookUrl(), 'https://hooks.slack.com/')
            && $this->form->is_pro;
    }
}
