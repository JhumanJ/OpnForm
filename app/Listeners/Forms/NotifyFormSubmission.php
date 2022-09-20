<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use App\Notifications\Forms\FormSubmissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyFormSubmission implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Sends notification to pre-defined emails on form submissions
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        if (!$event->form->notifies || !$event->form->is_pro) return;

        $subscribers = collect(preg_split("/\r\n|\n|\r/", $event->form->notification_emails))->filter(function($email) {
           return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        \Log::debug('Sending email notification',[
            'recipients' => $subscribers->toArray(),
            'form_id' => $event->form->id,
            'form_slug' => $event->form->slug,
        ]);
        $subscribers->each(function ($subscriber) use ($event) {
            Notification::route('mail', $subscriber)->notify(new FormSubmissionNotification($event));
        });
    }
}
