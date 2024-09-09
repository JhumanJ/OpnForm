<?php

use App\Mail\Forms\SubmissionConfirmationMail;
use Illuminate\Support\Facades\Mail;

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

it('creates confirmation emails with custom SMTP settings', function () {
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

    $integrationData = $this->createFormIntegration('submission_confirmation', $form->id, [
        'respondent_email' => true,
        'notifications_include_submission' => true,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Custom SMTP Test',
        'notification_body' => 'This email was sent using custom SMTP settings',
    ]);

    $formData = [
        collect($form->properties)->first(function ($property) {
            return $property['type'] == 'email';
        })['id'] => 'test@test.com',
    ];

    Mail::fake();

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);

    Mail::assertQueued(
        SubmissionConfirmationMail::class,
        function (SubmissionConfirmationMail $mail) {
            return $mail->hasTo('test@test.com') && $mail->mailer === 'custom_smtp';
        }
    );
});
