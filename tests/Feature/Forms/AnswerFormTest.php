<?php

use App\Models\Forms\Form;
use Tests\Helpers\FormSubmissionDataFactory;

it('can answer a form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // TODO: generate random response given a form and un-skip
})->skip('Need to finish writing a class to generated random responses');

it('can submit form if close date is in future', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'closes_at' => \Carbon\Carbon::now()->addDays(1)->toDateTimeString(),
    ]);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('can not submit closed form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'closes_at' => \Carbon\Carbon::now()->subDays(1)->toDateTimeString(),
    ]);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(403);
});

it('can submit form till max submissions count is not reached at limit', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'max_submissions_count' => 3,
    ]);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    // Can submit form
    for ($i = 1; $i <= 3; $i++) {
        $this->postJson(route('forms.answer', $form->slug), $formData)
            ->assertSuccessful()
            ->assertJson([
                'type' => 'success',
                'message' => 'Form submission saved.',
            ]);
    }

    // Now, can not submit form, Because it's reached at submission limit
    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(403);
});

it('can not open draft form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'visibility' => 'draft',
    ]);

    $this->getJson(route('forms.show', $form->slug))
        ->assertStatus(404);
});

it('can not submit draft form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'visibility' => 'draft',
    ]);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(403);
});

it('can not submit visibility closed form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'visibility' => 'closed',
    ]);
    $formData = FormSubmissionDataFactory::generateSubmissionData($form);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(403);
});

it('can not submit form with past dates', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $submissionData = [];
    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData) {
        if (in_array($property['type'], ['date'])) {
            $property['disable_past_dates'] = true;
            $submissionData[$property['id']] = now()->subDays(4)->format('Y-m-d');
        }

        return $property;
    })->toArray();
    $form->update();

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => 'The Date must be a date after yesterday.',
        ]);
});

it('can not submit form with future dates', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $submissionData = [];
    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData) {
        if (in_array($property['type'], ['date'])) {
            $property['disable_future_dates'] = true;
            $submissionData[$property['id']] = now()->addDays(4)->format('Y-m-d');
        }

        return $property;
    })->toArray();
    $form->update();

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => 'The Date must be a date before tomorrow.',
        ]);
});


it('can submit form with passed custom validation condition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Number')->first();
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'or',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'greater_than',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'number',
                        ],
                        'value' => 20,
                    ],
                ],
            ],
        ],
    ];
    $submissionData = [];
    $validationMessage = 'Number too low';
    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, $targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 100;
        }
        return $property;
    })->toArray();

    $form->update();
    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);
    $response->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('can not submit form with failed custom validation condition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Email')->first();
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'equals',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'email',
                        ],
                        'value' => 'test@gmail.com',
                    ],
                ],
            ],
        ],
    ];
    $submissionData = [];
    $validationMessage = 'Can only use test@gmail.com';
    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, &$targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 'fail@gmail.com';
        }
        return $property;
    })->toArray();

    $form->update();

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => $validationMessage,
        ]);
});


it('can validate form answer with precognition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $properties = $form->properties;
    $properties[0]['required'] = true;
    $properties[3]['required'] = true;
    $properties[6]['required'] = true;
    $properties[9]['required'] = true;

    $form->properties = $properties;
    $form->update();

    // Empty submission data should fail validation, with all 4 required fields
    $response = $this->postJson(route('forms.answer', $form->slug), []);
    $errors = $response->json()['errors'];
    $this->assertEquals(sizeof($errors), 4);
    $response->assertStatus(422);

    // Fill in data for only Name.
    $submissionData = [];
    foreach ($properties as $property) {
        if ($property['name'] == 'Name') {
            $submissionData[$property['id']] = 'Name';
        } else {
            $submissionData[$property['id']] = null;
        }
    }

    // Select only first 3 fields for precognition validation
    $validateOnlyFields = [
        $properties[0]['id'],
        $properties[1]['id'],
        $properties[2]['id']
    ];

    $precognitionValidateOnly = implode(',', $validateOnlyFields);

    // Partial submission data should pass validation for the precognition only fields.
    $response = $this->withPrecognition()->withHeaders([
        'Precognition-Validate-Only' => $precognitionValidateOnly
    ])
        ->postJson(route('forms.answer', $form->slug), $submissionData);

    $response->assertSuccessfulPrecognition();


    // Select only next fields for precognition validation
    $validateOnlyFields = $validateOnlyFields = [
        $properties[3]['id'],
        $properties[4]['id'],
        $properties[5]['id']
    ];
    $precognitionValidateOnly = implode(',', $validateOnlyFields);

    // Partial submission data should fail validation, but for only one required field specified for precognition validation.
    $response = $this->withPrecognition()->withHeaders([
        'Precognition-Validate-Only' => $precognitionValidateOnly
    ])
        ->postJson(route('forms.answer', $form->slug), $submissionData);
    $errors = $response->json()['errors'];
    $this->assertEquals(sizeof($errors), 1);
    $response->assertStatus(422);
});
