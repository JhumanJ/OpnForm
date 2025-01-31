<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\Helpers\FormSubmissionDataFactory;

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
        $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submission);
        $this->postJson(route('forms.answer', $form->slug), $formData)
            ->assertSuccessful();
    }

    // Test export with selected columns
    $response = $this->postJson(route('open.forms.submissions.export', [
        'id' => $form->id,
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
        'id' => $form->id,
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
    $user2 = User::factory()->create();
    $workspace = createUserWorkspace($user2);

    $form = createForm($user, $workspace);

    Sanctum::actingAs($user2);

    $response = $this->postJson(route('open.forms.submissions.export', [
        'id' => $form->id,
        'columns' => [
            'name_field' => true
        ]
    ]));

    $response->assertJson([
        'message' => 'Unauthenticated.'
    ]);
});
