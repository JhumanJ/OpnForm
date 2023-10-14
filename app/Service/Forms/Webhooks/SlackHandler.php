<?php

namespace App\Service\Forms\Webhooks;

use App\Service\Forms\FormSubmissionFormatter;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Arr;

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
        $settings = (array) Arr::get((array)$this->form->notification_settings, 'slack', []);
        $externalLinks = [];
        if(Arr::get($settings, 'link_open_form', true)){
            $externalLinks[] = '*<' . $this->form->share_url . '|🔗 Open Form>*';
        }
        if(Arr::get($settings, 'link_edit_form', true)){
            $editFormURL = url('forms/' . $this->form->slug . '/show');
            $externalLinks[] = '*<' . $editFormURL . '|✍️ Edit Form>*';
        }
        if (Arr::get($settings, 'link_edit_submission', true) && $this->form->editable_submissions) {
            $submissionId = Hashids::encode($this->data['submission_id']);
            $externalLinks[] = '*<' . $this->form->share_url . '?submission_id=' . $submissionId . '|✍️ ' . $this->form->editable_submissions_button_text . '>*';
        }

        $blocks = [
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => 'New submission for your form *' . $this->form->title . '*',
                ]
            ]
        ];

        if(Arr::get($settings, 'include_submission_data', true)){
            $submissionString = '';
            $formatter = (new FormSubmissionFormatter($this->form, $this->data))->outputStringsOnly();
            foreach ($formatter->getFieldsWithValue() as $field) {
                $tmpVal = is_array($field['value']) ? implode(',', $field['value']) : $field['value'];
                $submissionString .= '>*' . ucfirst($field['name']) . '*: ' . $tmpVal . " \n";
            }
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $submissionString,
                ]
            ];
        }

        if(Arr::get($settings, 'views_submissions_count', true)){
            $countString = '*👀 Views*: ' . (string)$this->form->views_count . " \n";
            $countString .= '*🖊️ Submissions*: ' . (string)$this->form->submissions_count;
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => $countString,
                ]
            ];
        }

        if(count($externalLinks) > 0){
            $blocks[] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => implode('     ', $externalLinks),
                ]
            ];
        }

        return [
            'blocks' => $blocks
        ];
    }

    protected function shouldRun(): bool
    {
        return !is_null($this->getWebhookUrl())
            && str_contains($this->getWebhookUrl(), 'https://hooks.slack.com/')
            && $this->form->is_pro;
    }
}
