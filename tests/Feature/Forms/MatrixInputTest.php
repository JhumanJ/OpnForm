<?php

use Tests\Helpers\FormSubmissionDataFactory;

it('can submit form with valid matrix input', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $matrixProperty = [
        'id' => 'matrix_field',
        'name' => 'Matrix Question',
        'type' => 'matrix',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$matrixProperty]);
    $form->update();

    $submissionData = [
        'matrix_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Column B',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('cannot submit form with invalid matrix input', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $matrixProperty = [
        'id' => 'matrix_field',
        'name' => 'Matrix Question',
        'type' => 'matrix',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$matrixProperty]);
    $form->update();

    $submissionData = [
        'matrix_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Invalid Column',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => "Invalid value 'Invalid Column' for row 'Row 2'.",
            'errors' => [
                'matrix_field' => [
                    "Invalid value 'Invalid Column' for row 'Row 2'."
                ]
            ]
        ]);
});

it('can submit form with optional matrix input left empty', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $matrixProperty = [
        'id' => 'matrix_field',
        'name' => 'Matrix Question',
        'type' => 'matrix',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => false
    ];

    $form->properties = array_merge($form->properties, [$matrixProperty]);
    $form->update();

    $submissionData = [
        'matrix_field' => []
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('cannot submit form with required matrix input left empty', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $matrixProperty = [
        'id' => 'matrix_field',
        'name' => 'Matrix Question',
        'type' => 'matrix',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$matrixProperty]);
    $form->update();

    $submissionData = [
        'matrix_field' => []
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => 'The Matrix Question field is required.',
            'errors' => [
                'matrix_field' => [
                    'The Matrix Question field is required.'
                ]
            ]
        ]);
});

it('can validate matrix input with precognition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $matrixProperty = [
        'id' => 'matrix_field',
        'name' => 'Matrix Question',
        'type' => 'matrix',
        'rows' => ['Row 1', 'Row 2', 'Row 3'],
        'columns' => ['Column A', 'Column B', 'Column C'],
        'required' => true
    ];

    $form->properties = array_merge($form->properties, [$matrixProperty]);
    $form->update();

    $submissionData = [
        'matrix_field' => [
            'Row 1' => 'Column A',
            'Row 2' => 'Invalid Column',
            'Row 3' => 'Column C'
        ]
    ];

    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $response = $this->withPrecognition()->withHeaders([
        'Precognition-Validate-Only' => 'matrix_field'
    ])
        ->postJson(route('forms.answer', $form->slug), $formData);

    $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                'matrix_field' => [
                    'Invalid value \'Invalid Column\' for row \'Row 2\'.'
                ]
            ]
        ]);
});
