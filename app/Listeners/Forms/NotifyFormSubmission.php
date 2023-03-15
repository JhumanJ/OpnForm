<?php

namespace App\Listeners\Forms;

use App\Models\Forms\Form;
use App\Events\Forms\FormSubmitted;
use Illuminate\Support\Facades\Http;
use Spatie\WebhookServer\WebhookCall;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Service\Forms\FormSubmissionFormatter;
use App\Notifications\Forms\FormSubmissionNotification;
use Vinkla\Hashids\Facades\Hashids;

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

        if ($event->form->notifies_discord) {
            // Send Discord Notification
            $this->sendDiscordNotification($event);
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
            $submissionId = Hashids::encode($event->data['submission_id']);
            $externalLinks = [
                '*<'.$formURL.'|🔗 Open Form>*',
                '*<'.$editFormURL.'|✍️ Edit Form>*'
            ];
            if($event->form->editable_submissions){
                $externalLinks[] = '*<'.$event->form->share_url.'?submission_id='.$submissionId.'|✍️ '.$event->form->editable_submissions_button_text.'>*';
            }

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
                            'text' => implode('     ', $externalLinks),
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

    private function sendDiscordNotification(FormSubmitted $event)
    {
        if($this->validateDiscordWebhookUrl($event->form->discord_webhook_url)){
            $submissionString = "";
            $formatter = (new FormSubmissionFormatter($event->form, $event->data))->outputStringsOnly();

            foreach ($formatter->getFieldsWithValue() as $field) {
                $tmpVal = is_array($field['value']) ? implode(",", $field['value']) : $field['value'];
                $submissionString .= "**".ucfirst($field['name'])."**: `".$tmpVal."`\n";
            }

            $form_name = $event->form->title;
            $form = Form::find($event->form->id);
            $formURL = url("forms/".$event->form->slug."/show/submissions");

            $finalDiscordPostData = [
                   "content" => "@here We have received a new submission for **$form_name**",
                   "username" => config('app.name'),
                   "avatar_url" => asset('img/logo.png'),
                   "tts" => false,
                   "embeds" => [
                       [
                           "title" => "🔗 Go to $form_name",

                           "type" => "rich",

                           "description" => $submissionString,

                           "url" => $formURL,

                           "color" => hexdec(str_replace('#', '', $event->form->color)),

                           "footer" => [
                               "text" => config('app.name'),
                               "icon_url" => asset('img/logo.png'),
                           ],

                           "author" => [
                               "name" => config('app.name'),
                               "url" => config('app.url'),
                           ],

                           "fields" => [
                               [
                                   "name" => "Views 👀",
                                   "value" => "$form->views_count",
                                   "inline" => true
                               ],
                               [
                                   "name" => "Submissions 🖊️",
                                   "value" => "$form->submissions_count",
                                   "inline" => true
                               ]
                           ]
                       ]
                   ]
           ];

            WebhookCall::create()
                ->url($event->form->discord_webhook_url)
                ->doNotSign()
                ->payload($finalDiscordPostData)
                ->dispatch();
        }
    }

    private function validateDiscordWebhookUrl($url)
    {
        return ($url) ? str_contains($url, 'https://discord.com/api/webhooks') : false;
    }
}
