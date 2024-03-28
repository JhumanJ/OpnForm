<?php

namespace App\Console\Commands;

use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use Illuminate\Console\Command;

class IntegrationMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:integration-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time Only -- Refactor integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Form::chunk(
            100,
            function ($forms) {
                foreach ($forms as $form) {
                    $this->line('Process For Form: ' . $form->id . ' - ' . $form->slug);

                    // Email
                    if ($form->notifies && $form->notification_emails) {
                        $this->createFormIntegration($form, 'email', [
                            'notification_reply_to' => $form->notification_settings->notification_reply_to,
                            'notification_emails' => $form->notification_emails
                        ]);
                    }

                    // Submission Confirmation
                    if ($form->send_submission_confirmation) {
                        $this->createFormIntegration($form, 'submission_confirmation', [
                            'confirmation_reply_to' => $form->notification_settings->confirmation_reply_to,
                            'notification_sender' => $form->notification_sender,
                            'notification_subject' => $form->notification_subject,
                            'notification_body' => $form->notification_body,
                            'notifications_include_submission' => $form->notifications_include_submission,
                        ]);
                    }

                    // Slack
                    if ($form->slack_webhook_url) {
                        $slackData = $form->notification_settings->slack;
                        $this->createFormIntegration($form, 'slack', [
                            'slack_webhook_url' => $form->slack_webhook_url,
                            'include_submission_data' => $slackData->include_submission_data ?? true,
                            'link_open_form' => $slackData->link_open_form ?? true,
                            'link_edit_form' => $slackData->link_edit_form ?? true,
                            'views_submissions_count' => $slackData->views_submissions_count ?? true,
                            'link_edit_submission' => $slackData->link_edit_submission ?? true
                        ]);
                    }

                    // Discord
                    if ($form->discord_webhook_url) {
                        $discordData = $form->notification_settings->discord;
                        $this->createFormIntegration($form, 'discord', [
                            'discord_webhook_url' => $form->discord_webhook_url,
                            'include_submission_data' => $discordData->include_submission_data ?? true,
                            'link_open_form' => $discordData->link_open_form ?? true,
                            'link_edit_form' => $discordData->link_edit_form ?? true,
                            'views_submissions_count' => $discordData->views_submissions_count ?? true,
                            'link_edit_submission' => $discordData->link_edit_submission ?? true
                        ]);
                    }

                    // Webhook
                    if ($form->webhook_url) {
                        $this->createFormIntegration($form, 'webhook', [
                            'webhook_url' => $form->webhook_url
                        ]);
                    }
                }
            }
        );

        $this->line('Migration Done');
    }

    private function createFormIntegration(Form $form, $integration_id, $data = [])
    {
        $this->line('Form Integration Create: ' . $integration_id);
        return FormIntegration::create([
            'form_id' => $form->id,
            'status' => FormIntegration::STATUS_ACTIVE,
            'integration_id' => $integration_id,
            'data' => $data,
            'logic' => []
        ]);
    }
}
