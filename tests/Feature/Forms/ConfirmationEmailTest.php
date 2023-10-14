<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\Forms\SubmissionConfirmationMail;

it('creates confirmation emails with the submitted data', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'send_submission_confirmation' => true,
        'notifications_include_submission' => true,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
    ]);

    $formData = [
        collect($form->properties)->first(function ($property) {
            return $property['type'] == 'email';
        })["id"] => "test@test.com",
    ];
    $event = new \App\Events\Forms\FormSubmitted($form, $formData);
    $mailable = new SubmissionConfirmationMail($event);
    $mailable->assertSeeInHtml('Test body')
        ->assertSeeInHtml('As a reminder, here are your answers:')
        ->assertSeeInHtml('You are receiving this email because you answered the form:');
});

it('creates confirmation emails without the submitted data', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'send_submission_confirmation' => true,
        'notifications_include_submission' => false,
        'notification_sender' => 'Custom Sender',
        'notification_subject' => 'Test subject',
        'notification_body' => 'Test body',
    ]);

    $formData = [
        collect($form->properties)->first(function ($property) {
            return $property['type'] == 'email';
        })["id"] => "test@test.com",
    ];
    $event = new \App\Events\Forms\FormSubmitted($form, $formData);
    $mailable = new SubmissionConfirmationMail($event);
    $mailable->assertSeeInHtml('Test body')
        ->assertDontSeeInHtml('As a reminder, here are your answers:')
        ->assertSeeInHtml('You are receiving this email because you answered the form:');
});

it('sends a confirmation email if needed', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'send_submission_confirmation' => true,
        'notifications_include_submission' => true,
        'notification_subject' => 'Test subject',
    ]);
    $emailProperty = collect($form->properties)->first(function ($property) {
        return $property['type'] == 'email';
    });
    $formData = [
        $emailProperty["id"] => "test@test.com",
    ];

    Mail::fake();

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.'
        ]);

    Mail::assertQueued(SubmissionConfirmationMail::class,
        function (SubmissionConfirmationMail $mail) use ($formData, $emailProperty) {
            return $mail->hasTo("test@test.com");
        });
});

it('does not send a confirmation email if not needed', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'send_submission_confirmation' => false,
    ]);
    $emailProperty = collect($form->properties)->first(function ($property) {
        return $property['type'] == 'email';
    });
    $formData = [
        $emailProperty["id"] => "test@test.com",
    ];

    Mail::fake();

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.'
        ]);

    Mail::assertNotQueued(SubmissionConfirmationMail::class,
        function (SubmissionConfirmationMail $mail) use ($formData, $emailProperty) {
            return $mail->hasTo("test@test.com");
        });
});

