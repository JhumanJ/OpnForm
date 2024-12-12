<?php

namespace App\Integrations\Handlers;

use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use App\Notifications\Forms\FormEmailNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Open\MentionParser;
use App\Service\Forms\FormSubmissionFormatter;
use Illuminate\Validation\ValidationException;

class EmailIntegration extends AbstractEmailIntegrationHandler
{
    public const RISKY_USERS_LIMIT = 120;

    public static function getValidationRules(?Form $form): array
    {
        $rules = [
            'send_to' => ['required'],
            'sender_name' => 'required',
            'sender_email' => 'email|nullable',
            'subject' => 'required',
            'email_content' => 'required',
            'include_submission_data' => 'boolean',
            'include_hidden_fields_submission_data' => ['nullable', 'boolean'],
            'reply_to' => 'nullable',
        ];

        if ($form->is_pro || config('app.self_hosted')) {
            return $rules;
        }

        // Free plan users can only send to a single email address (avoid spam)
        $rules['send_to'][] = function ($attribute, $value, $fail) use ($form) {
            if (count(explode("\n", trim($value))) > 1 || count(explode(',', $value)) > 1) {
                $fail('You can only send to a single email address on the free plan. Please upgrade to the Pro plan to create a new integration.');
            }
        };

        // Free plan users can only have a single email integration per form (avoid spam)
        if (!request()->route('integrationid')) {
            $existingEmailIntegrations = FormIntegration::where('form_id', $form->id)
                ->where('integration_id', 'email')
                ->count();
            if ($existingEmailIntegrations > 0) {
                throw ValidationException::withMessages([
                    'settings.send_to' => ['Free users are limited to 1 email integration per form.']
                ]);
            }
        }

        return $rules;
    }

    protected function shouldRun(): bool
    {
        return $this->integrationData?->send_to && parent::shouldRun() && !$this->riskLimitReached();
    }

    // To avoid phishing abuse we limit this feature for risky users
    private function riskLimitReached(): bool
    {
        // This is a per-workspace limit for risky workspaces
        if ($this->form->workspace->is_risky) {
            if ($this->form->workspace->submissions_count >= self::RISKY_USERS_LIMIT) {
                Log::error('!!!DANGER!!! Dangerous user detected! Attempting many email sending.', [
                    'form_id' => $this->form->id,
                    'workspace_id' => $this->form->workspace->id,
                ]);

                return true;
            }
        }

        return false;
    }

    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        if ($this->form->is_pro) {  // For Send to field Mentions are Pro feature
            $formatter = (new FormSubmissionFormatter($this->form, $this->submissionData))->outputStringsOnly();
            $parser = new MentionParser($this->integrationData?->send_to, $formatter->getFieldsWithValue());
            $sendTo = $parser->parseAsText();
        } else {
            $sendTo = $this->integrationData?->send_to;
        }

        $recipients = collect(preg_split("/\r\n|\n|\r/", $sendTo))
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        Log::debug('Sending email notification', [
            'recipients' => $recipients->toArray(),
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
            'mailer' => $this->mailer
        ]);

        $recipients->each(function ($subscriber) {
            Notification::route('mail', $subscriber)->notify(
                new FormEmailNotification($this->event, $this->integrationData, $this->mailer)
            );
        });
    }
}
