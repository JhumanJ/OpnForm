<?php

namespace App\Console\Commands;

use App\Models\Forms\Form;
use App\Models\Integration\FormIntegration;
use Illuminate\Console\Command;

class EmailNotificationMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:email-notification-migration {--dry : Log changes without applying them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time Only -- Migrate Email & Submission Notifications to new Email Integration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (app()->environment('production')) {
            if (!$this->confirm('Are you sure you want to run this migration in production?')) {
                $this->info('Migration aborted.');
                return 0;
            }
        }
        $query = FormIntegration::whereIn('integration_id', ['email', 'submission_confirmation'])
            ->whereHas('form');
        $totalCount = $query->count();
        $progressBar = $this->output->createProgressBar($totalCount);
        $progressBar->start();

        $isDryRun = $this->option('dry');

        $query->with('form')->chunk(100, function ($integrations) use ($progressBar, $isDryRun) {
            foreach ($integrations as $integration) {
                try {
                    $this->updateIntegration($integration, $isDryRun);
                } catch (\Exception $e) {
                    $this->error('Error updating integration ' . $integration->id . '. Error: ' . $e->getMessage());
                    ray($e);
                }
                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();

        $this->line('Migration Done');
    }

    public function updateIntegration(FormIntegration $integration, $isDryRun = false)
    {
        if (!$integration->form) {
            return;
        }
        $existingData = $integration->data;
        if ($integration->integration_id === 'email' && isset($existingData->notification_emails)) {
            $newData = [
                'send_to' => $existingData->notification_emails ?? null,
                'sender_name' => 'OpnForm',
                'subject' => 'New form submission',
                'email_content' => 'Hello there 👋 <br>New form submission received.',
                'include_submission_data' => true,
                'include_hidden_fields_submission_data' => false,
                'reply_to' => $existingData->notification_reply_to ?? null
            ];
            if ($isDryRun) {
                $this->info('Dry run: Would update integration ' . $integration->id . ' with data: ' . json_encode($newData));
            } else {
                $integration->data = $newData;
                return $integration->save();
            }
        } elseif ($integration->integration_id === 'submission_confirmation' && isset($existingData->notification_subject)) {
            $newData = [
                'send_to' => $this->getMentionHtml($integration->form),
                'sender_name' => $existingData->notification_sender,
                'subject' => $existingData->notification_subject,
                'email_content' => $existingData->notification_body,
                'include_submission_data' => $existingData->notifications_include_submission,
                'include_hidden_fields_submission_data' => false,
                'reply_to' => $existingData->confirmation_reply_to ?? null
            ];
            if ($isDryRun) {
                $this->info('Dry run: Would update integration ' . $integration->id . ' with data: ' . json_encode($newData));
            } else {
                $integration->integration_id = 'email';
                $integration->data = $newData;
                return $integration->save();
            }
        }
        return;
    }

    private function getMentionHtml(Form $form)
    {
        $emailField = $this->getRespondentEmail($form);
        return $emailField ? '<span mention-field-id="' . $emailField['id'] . '" mention-field-name="' . $emailField['name'] . '" mention-fallback="" contenteditable="false" mention="true">' . $emailField['name'] . '</span>' : '';
    }

    private function getRespondentEmail(Form $form)
    {
        $emailFields = collect($form->properties)->filter(function ($field) {
            $hidden = $field['hidden'] ?? false;
            return !$hidden && $field['type'] == 'email';
        });

        return $emailFields->count() > 0 ? $emailFields->first() : null;
    }
}
