<?php

use App\Notifications\Forms\FormEmailNotification;
use Tests\Helpers\FormSubmissionDataFactory;
use Illuminate\Notifications\AnonymousNotifiable;

it('can not save custom SMTP settings if not pro user', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    $this->putJson(route('open.workspaces.save-email-settings', [$workspace->id]), [
        'host' => 'custom.smtp.host',
        'port' => '587',
        'username' => 'custom_username',
        'password' => 'custom_password',
    ])->assertStatus(403);
});

it('send email with custom SMTP settings', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // Set custom SMTP settings
    $this->putJson(route('open.workspaces.save-email-settings', [$workspace->id]), [
        'host' => 'custom.smtp.host',
        'port' => '587',
        'username' => 'custom_username',
        'password' => 'custom_password',
    ])->assertSuccessful();

    $integrationData = $this->createFormIntegration('email', $form->id, [
        'send_to' => 'test@test.com',
        'sender_name' => 'OpnForm',
        'subject' => 'New form submission',
        'email_content' => 'Hello there 👋 <br>New form submission received.',
        'include_submission_data' => true,
        'include_hidden_fields_submission_data' => false,
        'reply_to' => 'reply@example.com',
    ]);

    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    Notification::fake();

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);

    Notification::assertSentTo(
        new AnonymousNotifiable(),
        FormEmailNotification::class,
        function (FormEmailNotification $notification, $channels, $notifiable) {
            return $notifiable->routes['mail'] === 'test@test.com' &&
                $notification->mailer === 'custom_smtp';
        }
    );
});
