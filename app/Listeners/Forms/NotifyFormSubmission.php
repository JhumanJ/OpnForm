<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use App\Notifications\Forms\FormSubmissionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Spatie\WebhookServer\WebhookCall;
use App\Service\Forms\FormSubmissionFormatter;

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
        if (!$event->form->is_pro) return;

        if ($event->form->notifies) {
            // Send Email Notification
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

        if ($event->form->notifies_slack) {
            // Send Slack Notification
            $this->sendSlackNotification($event);
        }
    }

    private function sendSlackNotification(FormSubmitted $event)
    {
        if($this->validateSlackWebhookUrl($event->form->slack_webhook_url)){
            $submissionString = "";
            $formatter = (new FormSubmissionFormatter($event->form, $event->data))->outputStringsOnly();
            foreach ($formatter->getFieldsWithValue() as $field) {
                $tmpVal = is_array($field['value']) ? implode(",", $field['value']) : $field['value'];
                $submissionString .= ">*".ucfirst($field['name'])."*: ".$tmpVal." \n";
            }

            $formURL = url("forms/".$event->form->slug);
            $editFormURL = url("forms/".$event->form->slug."/show");
            $finalSlackPostData = [
                'blocks' => [
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => 'New submission for your form *<'.$formURL.'|'.$event->form->title.':>*',
                        ]
                    ],
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => $submissionString
                        ]
                    ],
                    [
                        'type' => 'section',
                        'text' => [
                            'type' => 'mrkdwn',
                            'text' => '*<'.$formURL.'|🔗 Open Form>*     *<'.$editFormURL.'|✍️ Edit Form>*',
                        ]
                    ],
                ]
            ];

            WebhookCall::create()
                ->url($event->form->slack_webhook_url)
                ->doNotSign()
                ->payload($finalSlackPostData)
                ->dispatch();
        }
    }

    private function validateSlackWebhookUrl($url)
    {
        return ($url) ? str_contains($url, 'https://hooks.slack.com/') : false;
    }
}
