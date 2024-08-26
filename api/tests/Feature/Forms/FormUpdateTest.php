<?php

use Tests\Helpers\FormSubmissionDataFactory;

it('can update form with existing record', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'editable_submissions' => true,
    ]);

    $nameProperty = collect($form->properties)->filter(function ($property) {
        return $property['name'] == 'Name';
    })->first();

    $response = $this->postJson(route('forms.answer', $form->slug), [$nameProperty['id'] => 'Testing'])
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
    $submissionId = $response->json('submission_id');
    expect($submissionId)->toBeString();

    if ($submissionId) {
        $formData = FormSubmissionDataFactory::generateSubmissionData($form, ['submission_id' => $submissionId, $nameProperty['id'] => 'Testing Updated']);
        $response = $this->postJson(route('forms.answer', $form->slug), $formData)
            ->assertSuccessful()
            ->assertJson([
                'type' => 'success',
                'message' => 'Form submission saved.',
            ]);
        $submissionId2 = $response->json('submission_id');
        expect($submissionId2)->toBeString();
        expect($submissionId2)->toBe($submissionId);

        $response = $this->getJson(route('forms.fetchSubmission', [$form->slug, $submissionId]))
            ->assertSuccessful();
        expect($response->json('data.'.$nameProperty['id']))->toBe('Testing Updated');
    }
});
