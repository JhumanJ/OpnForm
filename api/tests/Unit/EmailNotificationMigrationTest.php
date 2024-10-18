<?php

namespace Tests\Unit;

use App\Console\Commands\EmailNotificationMigration;
use App\Models\Integration\FormIntegration;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->command = new EmailNotificationMigration();
});

it('updates email integration correctly', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $integration = FormIntegration::create([
        'integration_id' => 'email',
        'form_id' => $form->id,
        'status' => FormIntegration::STATUS_ACTIVE,
        'data' => [
            'notification_emails' => 'test@example.com',
            'notification_reply_to' => 'reply@example.com',
        ],
    ]);

    $this->command->updateIntegration($integration);

    expect($integration->fresh())
        ->integration_id->toBe('email')
        ->data->toMatchArray([
            'send_to' => 'test@example.com',
            'sender_name' => 'OpnForm',
            'subject' => 'New form submission',
            'email_content' => 'Hello there 👋 <br>New form submission received.',
            'include_submission_data' => true,
            'include_hidden_fields_submission_data' => false,
            'reply_to' => 'reply@example.com',
        ]);
});

it('updates submission confirmation integration correctly', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $emailProperty = collect($form->properties)->filter(function ($property) {
        return $property['type'] == 'email';
    })->first();

    $integration = FormIntegration::create([
        'integration_id' => 'submission_confirmation',
        'form_id' => $form->id,
        'status' => FormIntegration::STATUS_ACTIVE,
        'data' => [
            'notification_sender' => 'Sender Name',
            'notification_subject' => 'Thank you for your submission',
            'notification_body' => 'We received your submission.',
            'notifications_include_submission' => true,
            'confirmation_reply_to' => 'reply@example.com',
        ],
    ]);

    $this->command->updateIntegration($integration);

    expect($integration->fresh())
        ->integration_id->toBe('email')
        ->data->toMatchArray([
            'send_to' => '<span mention-field-id="' . $emailProperty['id'] . '" mention-field-name="' . $emailProperty['name'] . '" mention-fallback="" contenteditable="false" mention="true">' . $emailProperty['name'] . '</span>',
            'sender_name' => 'Sender Name',
            'subject' => 'Thank you for your submission',
            'email_content' => 'We received your submission.',
            'include_submission_data' => true,
            'include_hidden_fields_submission_data' => false,
            'reply_to' => 'reply@example.com',
        ]);
});
