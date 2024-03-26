<?php

namespace App\Service\Forms\Integrations;

use App\Mail\Forms\SubmissionConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stevebauman\Purify\Facades\Purify;

/**
 * Sends a confirmation to form respondant that form was submitted
 */
class SubmissionConfirmationIntegration extends AbstractIntegrationHandler
{
    public const RISKY_USERS_LIMIT = 120;

    public static function getValidationRules(): array
    {
        return [
            'confirmation_reply_to' => 'email|nullable',
            'notification_sender' => 'required',
            'notification_subject' => 'required',
            'notification_body' => 'required',
            'notifications_include_submission' => 'boolean'
        ];
    }

    protected function shouldRun(): bool
    {
        return !(!$this->form->is_pro) && parent::shouldRun() && !$this->riskLimitReached();
    }

    public function handle(): void
    {
        if (!$this->shouldRun()) {
            return;
        }

        $email = $this->getRespondentEmail();
        if (!$email) {
            return;
        }

        Log::info('Sending submission confirmation', [
            'recipient' => $email,
            'form_id' => $this->form->id,
            'form_slug' => $this->form->slug,
        ]);
        Mail::to($email)->send(new SubmissionConfirmationMail($this->event, $this->integrationData));
    }

    private function getRespondentEmail()
    {
        // Make sure we only have one email field in the form
        $emailFields = collect($this->form->properties)->filter(function ($field) {
            $hidden = $field['hidden'] ?? false;

            return !$hidden && $field['type'] == 'email';
        });
        if ($emailFields->count() != 1) {
            return null;
        }

        if (isset($this->submissionData[$emailFields->first()['id']])) {
            $email = $this->submissionData[$emailFields->first()['id']];
            if ($this->validateEmail($email)) {
                return $email;
            }
        }

        return null;
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

    public static function validateEmail($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function formatData(array $data): array
    {
        return array_merge(parent::formatData($data), [
            'notification_body' => Purify::clean($data['notification_body'] ?? ''),
        ]);
    }
}
