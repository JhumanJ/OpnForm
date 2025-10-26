<?php

use App\Models\Forms\FormSubmission;
use App\Models\User;

it('can export form submissions with status column when partial submissions are enabled', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
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

    // Create completed submission
    $completedSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe', 'email_field' => 'john@example.com'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    // Create partial submission
    $partialSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'Jane Smith'],
        'status' => FormSubmission::STATUS_PARTIAL
    ]);

    // Test export with status column included
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'email_field' => true,
            'status' => true,
            'created_at' => true
        ]
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});

it('cannot include status column when partial submissions are disabled', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => false,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Try to export with status column when partial submissions are disabled
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ]
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['columns']);
});

it('can filter export by completed submissions only', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create completed submission
    $completedSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    // Create partial submission
    $partialSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'Jane Smith'],
        'status' => FormSubmission::STATUS_PARTIAL
    ]);

    // Export only completed submissions
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ],
        'status_filter' => 'completed'
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});

it('can filter export by partial submissions only', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create completed submission
    $completedSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    // Create partial submission
    $partialSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'Jane Smith'],
        'status' => FormSubmission::STATUS_PARTIAL
    ]);

    // Export only partial submissions
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ],
        'status_filter' => 'partial'
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});

it('exports all submissions when status_filter is all or not provided', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create completed submission
    $completedSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    // Create partial submission
    $partialSubmission = $form->submissions()->create([
        'data' => ['name_field' => 'Jane Smith'],
        'status' => FormSubmission::STATUS_PARTIAL
    ]);

    // Export all submissions (default behavior)
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ]
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');

    // Test with explicit 'all' filter
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ],
        'status_filter' => 'all'
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});

it('validates status_filter parameter', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Test invalid status filter
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true
        ],
        'status_filter' => 'invalid'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status_filter']);
});

it('handles async export with status filtering', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create many submissions to trigger async export (threshold is 1000)
    // Create 1001 completed submissions to ensure we exceed threshold even after filtering
    for ($i = 0; $i < 1001; $i++) {
        $form->submissions()->create([
            'data' => ['name_field' => "User $i"],
            'status' => FormSubmission::STATUS_COMPLETED
        ]);
    }
    
    // Add some partial submissions too
    for ($i = 0; $i < 100; $i++) {
        $form->submissions()->create([
            'data' => ['name_field' => "Partial User $i"],
            'status' => FormSubmission::STATUS_PARTIAL
        ]);
    }

    // Test async export with status filter
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true,
            'status' => true
        ],
        'status_filter' => 'completed'
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'is_async' => true
        ])
        ->assertJsonStructure([
            'job_id',
            'message'
        ]);
});

it('exports without status column when not requested even if partial submissions enabled', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'enable_partial_submissions' => true,
        'properties' => [
            [
                'id' => 'name_field',
                'name' => 'Name',
                'type' => 'text',
                'required' => true,
            ]
        ]
    ]);

    // Create submissions
    $form->submissions()->create([
        'data' => ['name_field' => 'John Doe'],
        'status' => FormSubmission::STATUS_COMPLETED
    ]);

    // Export without status column
    $response = $this->postJson(route('open.forms.submissions.export', ['form' => $form]), [
        'columns' => [
            'name_field' => true
        ]
    ]);

    $response->assertSuccessful()
        ->assertHeader('content-disposition', 'attachment; filename=' . $form->slug . '-submission-data.csv');
});