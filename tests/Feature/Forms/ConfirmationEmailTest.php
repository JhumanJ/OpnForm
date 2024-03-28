<?php

use App\Mail\Forms\SubmissionConfirmationMail;
use Illuminate\Support\Facades\Mail;

it('creates confirmation emails with the submitted data', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $integrationData = $this->createFormIntegration('submission_confirmation', $form->id, [
        'notifications_include_submission' => true,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
    ]);

    $formData = [
        collect($form->properties)->first(function ($property) {
            return $property['type'] == 'email';
        })['id'] => 'test@test.com',
    ];
    $event = new \App\Events\Forms\FormSubmitted($form, $formData);
    $mailable = new SubmissionConfirmationMail($event, $integrationData);
    $mailable->assertSeeInHtml('Test body')
        ->assertSeeInHtml('As a reminder, here are your answers:')
        ->assertSeeInHtml('You are receiving this email because you answered the form:');
});

it('creates confirmation emails without the submitted data', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $integrationData = $this->createFormIntegration('submission_confirmation', $form->id, [
        'notifications_include_submission' => false,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
    ]);

    $formData = [
        collect($form->properties)->first(function ($property) {
            return $property['type'] == 'email';
        })['id'] => 'test@test.com',
    ];
    $event = new \App\Events\Forms\FormSubmitted($form, $formData);
    $mailable = new SubmissionConfirmationMail($event, $integrationData);
    $mailable->assertSeeInHtml('Test body')
        ->assertDontSeeInHtml('As a reminder, here are your answers:')
        ->assertSeeInHtml('You are receiving this email because you answered the form:');
});

it('sends a confirmation email if needed', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->createFormIntegration('submission_confirmation', $form->id, [
        'notifications_include_submission' => true,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
    ]);

    $emailProperty = collect($form->properties)->first(function ($property) {
        return $property['type'] == 'email';
    });
    $formData = [
        $emailProperty['id'] => 'test@test.com',
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
            return $mail->hasTo('test@test.com');
        }
    );
});

it('does not send a confirmation email if not needed', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $emailProperty = collect($form->properties)->first(function ($property) {
        return $property['type'] == 'email';
    });
    $formData = [
        $emailProperty['id'] => 'test@test.com',
    ];

    Mail::fake();

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);

    Mail::assertNotQueued(
        SubmissionConfirmationMail::class,
        function (SubmissionConfirmationMail $mail) {
            return $mail->hasTo('test@test.com');
        }
    );
});

it('does send a confirmation email even when reply to is broken', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $integrationData = $this->createFormIntegration('submission_confirmation', $form->id, [
        'notifications_include_submission' => true,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
        'confirmation_reply_to' => ''
    ]);

    $emailProperty = collect($form->properties)->first(function ($property) {
        return $property['type'] == 'email';
    });
    $formData = [
        $emailProperty['id'] => 'test@test.com',
    ];
    $event = new \App\Events\Forms\FormSubmitted($form, $formData);
    $mailable = new SubmissionConfirmationMail($event, $integrationData);
    $mailable->assertSeeInHtml('Test body')
        ->assertSeeInHtml('As a reminder, here are your answers:')
        ->assertSeeInHtml('You are receiving this email because you answered the form:')
        ->assertHasReplyTo($user->email); // Even though reply to is wrong, it should use the user's email
});
