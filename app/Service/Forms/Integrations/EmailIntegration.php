<?php

namespace App\Service\Forms\Integrations;

use App\Rules\OneEmailPerLine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Forms\FormSubmissionNotification;

class EmailIntegration extends AbstractIntegrationHandler
{
    public static function getValidationRules(): array
    {
        return [
            'notification_emails' => ['required', new OneEmailPerLine()],
            'notification_reply_to' => 'email|nullable',
        ];
    }

    protected function shouldRun(): bool
    {
        return $this->integrationData->notification_emails && parent::shouldRun();
    }

    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $subscribers = collect(preg_split("/\r\n|\n|\r/", $this->integrationData->notification_emails))
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        Log::debug('Sending email notification', [
            'recipients' => $subscribers->toArray(),
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);
        $subscribers->each(function ($subscriber) {
            Notification::route('mail', $subscriber)->notify(
                new FormSubmissionNotification($this->event, $this->integrationData)
            );
        });
    }
}
