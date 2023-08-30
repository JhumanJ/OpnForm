<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use App\Mail\Forms\SubmissionConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Sends a confirmation to form respondant that form was submitted
 *
 * Class SubmissionConfirmation
 * @package App\Listeners\Forms
 */
class SubmissionConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    const RISKY_USERS_LIMIT = 120;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        if (
            !$event->form->is_pro ||
            !$event->form->send_submission_confirmation ||
            $this->riskLimitReached($event) // To avoid phishing abuse we limit this feature for risky users
        ) {
            return;
        }

        $email = $this->getRespondentEmail($event);
        if (!$email) return;

        \Log::info('Sending submission confirmation',[
            'recipient' => $email,
            'form_id' => $event->form->id,
            'form_slug' => $event->form->slug,
        ]);
        Mail::to($email)->send(new SubmissionConfirmationMail($event));
    }

    private function getRespondentEmail(FormSubmitted $event)
    {
        // Make sure we only have one email field in the form
        $emailFields = collect($event->form->properties)->filter(function($field) {
            $hidden = $field['hidden']?? false;
            return !$hidden && $field['type'] == 'email';
        });
        if ($emailFields->count() != 1) return null;

        if (isset($event->data[$emailFields->first()['id']])) {
            $email = $event->data[$emailFields->first()['id']];
            if ($this->validateEmail($email)) return $email;
        }

        return null;
    }

    private function riskLimitReached(FormSubmitted $event): bool
    {
        // This is a per-workspace limit for risky workspaces
        if ($event->form->workspace->is_risky) {
            if ($event->form->workspace->submissions_count >= self::RISKY_USERS_LIMIT) {
                \Log::error('!!!DANGER!!! Dangerous user detected! Attempting many email sending.', [
                    'form_id' => $event->form->id,
                    'workspace_id' => $event->form->workspace->id,
                ]);
                return true;
            }
        }
        return false;
    }

    public static function validateEmail($email): bool {
        return (boolean) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
