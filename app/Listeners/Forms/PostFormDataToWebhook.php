<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use App\Models\Forms\Form;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\WebhookCall;
use App\Service\Forms\FormSubmissionFormatter;
use Vinkla\Hashids\Facades\Hashids;
class PostFormDataToWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        $form = $event->form;
        if (!$form->is_pro) return;
        $data = $this->getWebhookData($event);

        $this->sendSimpleWebhook($form, $data);
        $this->sendZappierWebhooks($form, $data);
    }

    private function sendSimpleWebhook(Form $form, array $data) {
        if ($form->webhook_url) {
            \Log::debug('Sending data to webhook URL',[
                'webhook_url' => $form->webhook_url,
                'form_id' => $form->id,
                'form_slug' => $form->slug,
            ]);
            WebhookCall::create()
                ->url($form->webhook_url)
                ->doNotSign()
                ->payload($data)
                ->dispatch();
        }
    }

    private function sendZappierWebhooks(Form $form, array $data) {
        foreach ($form->zappierHooks as $hook) {
            \Log::debug('Sending data to Zapier webhook',[
                'form_id' => $form->id,
                'form_slug' => $form->slug,
            ]);
            $hook->triggerHook($data);
        }
    }

    private function getWebhookData(FormSubmitted $event): array {
        $formatter = (new FormSubmissionFormatter($event->form, $event->data))->showHiddenFields();

        $formattedData = [];
        foreach ($formatter->getFieldsWithValue() as $field) {
            $formattedData[$field['name']] = $field['value'];
        }

        $submissionId = Hashids::encode($event->data['submission_id']);
        $data = [
            'form_title' => $event->form->title,
            'form_slug' => $event->form->slug,
            'submission' => $formattedData
        ];
        if($event->form->editable_submissions){
            $data['edit_link'] = $event->form->share_url.'?submission_id='.$submissionId;
        }

        return $data;
    }
}
