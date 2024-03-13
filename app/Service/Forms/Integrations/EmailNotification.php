<?php

namespace App\Service\Forms\Integrations;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Forms\FormSubmissionNotification;

class EmailNotification extends AbstractIntegrationHandler
{
    protected function shouldRun(): bool
    {
        return !(!$this->form->is_pro || !$this->formIntegrationData->notification_emails) && parent::shouldRun();
    }

    public function handle()
    {
        if (!$this->shouldRun()) {
            return;
        }

        $subscribers = collect(preg_split("/\r\n|\n|\r/", $this->formIntegrationData->notification_emails))
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        Log::debug('Sending email notification', [
            'recipients' => $subscribers->toArray(),
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);
        $subscribers->each(function ($subscriber) {
            Notification::route('mail', $subscriber)->notify(new FormSubmissionNotification($this->event));
        });
    }
}
