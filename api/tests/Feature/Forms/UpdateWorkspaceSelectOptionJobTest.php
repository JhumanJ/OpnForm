<?php

use App\Models\User;
use Tests\Helpers\FormSubmissionDataFactory;

it('can validate Update Workspace Select Option Job', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);

    $formData = FormSubmissionDataFactory::generateSubmissionData($form);
    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('can validate scope with active subscription', function () {
    $this->createProUser();
    $this->createUser();
    $this->createProUser();
    $this->createProUser();
    $this->createUser();

    expect(User::WithActiveSubscription()->count())->toBe(3);
});
