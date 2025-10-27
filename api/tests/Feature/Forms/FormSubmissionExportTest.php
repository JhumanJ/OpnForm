<?php

use App\Models\User;

it('can export form submissions with selected columns', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ],
            [
                'id' => 'email_field',
                'name' => 'Email',
                'type' => 'email',
                'required' => true,
            ]
        ]
    ]);

    // Create some submissions
    $submissions = [
        ['name_field' => 'John Doe', 'email_field' => 'john@example.com'],
        ['name_field' => 'Jane Smith', 'email_field' => 'jane@example.com']
    ];

    foreach ($submissions as $submission) {
        $formData = $this->generateFormSubmissionData($form, $submission);
        $this->postJson(route('forms.answer', $form->slug), $formData)
            ->assertSuccessful();
    }

    // Test export with selected columns
    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            'name_field' => true,
            'email_field' => true,
            'created_at' => true
        ]
    ]));

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});

it('cannot export form submissions with invalid columns', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            'invalid_field' => true,
            'name_field' => true
        ]
    ]));

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['columns']);
});

it('cannot export form submissions from another user form', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form = createForm($user, $workspace);

    $this->actingAsProUser();

    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => []
    ]));

    $response->assertJson([
        'message' => 'This action is unauthorized.'
    ]);
});

it('includes status column when partial submissions are enabled', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
<<<<<<< HEAD
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ],
            [
                'id' => 'email_field',
                'name' => 'Email',
                'type' => 'email',
                'required' => true,
            ]
        ]
    ]);

    // Create a partial submission (In Progress)
    $partialSubmissionData = $this->generateFormSubmissionData($form, [
        'name_field' => 'John Partial',
    ]);
    $partialSubmissionData['is_partial'] = true;
    $this->postJson(route('forms.answer', $form->slug), $partialSubmissionData);

    // Create a completed submission
    $completedSubmissionData = $this->generateFormSubmissionData($form, [
        'name_field' => 'Jane Complete',
        'email_field' => 'jane@example.com'
    ]);
    $this->postJson(route('forms.answer', $form->slug), $completedSubmissionData);

    // Export with selected columns
    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            'name_field' => true,
            'email_field' => true,
=======
    ]);

    // Create a partial submission (In Progress)
    $textField = collect($form->properties)->firstWhere('type', 'text');
    $partialSubmissionData = $this->generateFormSubmissionData($form, [
        $textField['id'] => 'John Partial',
    ]);
    $partialSubmissionData['is_partial'] = true;
    $partialResponse = $this->postJson(route('forms.answer', $form->slug), $partialSubmissionData);
    $partialResponse->assertSuccessful();

    // Create a completed submission
    $emailField = collect($form->properties)->firstWhere('type', 'email');
    $completedSubmissionData = $this->generateFormSubmissionData($form, [
        $textField['id'] => 'Jane Complete',
        $emailField['id'] => 'jane@example.com'
    ]);
    $completedResponse = $this->postJson(route('forms.answer', $form->slug), $completedSubmissionData);
    $completedResponse->assertSuccessful();

    // Verify counts before export
    $form->refresh();
    $total = $form->submissions()->count();
    $partialCount = $form->submissions()->where('status', \App\Models\Forms\FormSubmission::STATUS_PARTIAL)->count();

    // Export with selected columns (use real field ids)
    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            $textField['id'] => true,
            $emailField['id'] => true,
>>>>>>> f011505849c7638f8250264957a9b95f1626d0df
        ]
    ]));

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');

    // Verify the exported CSV contains status column and correct values
<<<<<<< HEAD
    $content = $response->getContent();
    $lines = explode("\n", $content);

    // Check that CSV has at least 3 lines (header + 2 data rows)
    expect(count($lines))->toBeGreaterThanOrEqual(3);

    // Check header contains 'status'
    $headerLine = trim($lines[0]);
    expect($headerLine)->toContain('status');

    // Check that data rows contain 'In Progress' or 'Completed'
    $dataContent = implode("\n", array_slice($lines, 1));
    expect(str_contains($dataContent, 'In Progress') || str_contains($dataContent, 'Completed'))->toBeTrue();
=======
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect(str_contains($content, 'status'))->toBeTrue();
    expect(str_contains($content, 'In Progress'))->toBeTrue();
    expect(str_contains($content, 'Completed'))->toBeTrue();
>>>>>>> f011505849c7638f8250264957a9b95f1626d0df
});

it('does not include status column when partial submissions are disabled', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => false,
<<<<<<< HEAD
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create a submission
    $submissionData = $this->generateFormSubmissionData($form, [
        'name_field' => 'John Doe',
    ]);
    $this->postJson(route('forms.answer', $form->slug), $submissionData);

    // Export with selected columns
    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            'name_field' => true,
=======
    ]);

    // Create a submission
    $textField = collect($form->properties)->firstWhere('type', 'text');
    $submissionData = $this->generateFormSubmissionData($form, [
        $textField['id'] => 'John Doe',
    ]);
    $this->postJson(route('forms.answer', $form->slug), $submissionData);

    // Export with selected columns (use a real field id)
    $response = $this->postJson(route('open.forms.submissions.export', [
        'form' => $form,
        'columns' => [
            $textField['id'] => true,
>>>>>>> f011505849c7638f8250264957a9b95f1626d0df
        ]
    ]));

    $response->assertSuccessful();

    // Verify the exported CSV does not contain status column
<<<<<<< HEAD
    $content = $response->getContent();
=======
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

>>>>>>> f011505849c7638f8250264957a9b95f1626d0df
    expect(str_contains($content, 'status'))->toBeFalse();
});
