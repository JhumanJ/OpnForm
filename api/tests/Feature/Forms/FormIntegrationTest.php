<?php

it('can CRUD form integration', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $data = [
        'status' => true,
        'integration_id' => 'email',
        'logic' => null,
        'settings' => [
            'send_to' => 'test@test.com',
            'sender_name' => 'OpnForm',
            'subject' => 'New form submission',
            'email_content' => 'Hello there 👋 <br>New form submission received.',
            'include_submission_data' => true,
            'include_hidden_fields_submission_data' => false,
            'reply_to' => null
        ]
    ];

    $response = $this->postJson(route('open.forms.integrations.create', $form), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was created.'
        ]);

    $this->getJson(route('open.forms.integrations.index', $form))
        ->assertSuccessful()
        ->assertJsonCount(1);

    $this->putJson(route('open.forms.integrations.update', [$form, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was updated.'
        ]);

    $this->deleteJson(route('open.forms.integrations.destroy', [$form, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was deleted.'
        ]);
});

it('forbids non-admin users from viewing integrations and events', function () {
    $owner = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($owner);
    $form = $this->createForm($owner, $workspace);

    // Create one integration as owner
    $this->postJson(route('open.forms.integrations.create', $form), [
        'status' => true,
        'integration_id' => 'email',
        'logic' => null,
        'settings' => [
            'send_to' => 'test@test.com',
            'sender_name' => 'OpnForm',
            'subject' => 'Subject',
            'email_content' => 'Content',
            'include_submission_data' => false,
            'include_hidden_fields_submission_data' => false,
            'reply_to' => null
        ]
    ])->assertSuccessful();

    // Attach viewer and try to read
    $viewer = $this->createUser();
    $viewer->workspaces()->sync([$workspace->id => ['role' => 'readonly']], false);
    $this->actingAs($viewer, 'api');

    $this->getJson(route('open.forms.integrations.index', $form))
        ->assertStatus(403);

    // Events require an integration id; fetch as owner first
    $this->actingAs($owner, 'api');
    $integrationId = \App\Models\Integration\FormIntegration::where('form_id', $form->id)->value('id');

    // Now as viewer, events should be forbidden as well
    $this->actingAs($viewer, 'api');
    $this->getJson(route('open.forms.integrations.events', [$form, $integrationId]))
        ->assertStatus(403);
});

it('can create form integration with checkbox logic', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'checkbox_field',
                'name' => 'Checkbox Field',
                'type' => 'checkbox'
            ],
            [
                'id' => 'text_field',
                'name' => 'Text Field',
                'type' => 'text',
            ],
        ],
    ]);

    $data = [
        'status' => true,
        'integration_id' => 'email',
        'logic' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => 'checkbox_field',
                    'value' => [
                        'operator' => 'is_checked',
                        'property_meta' => [
                            'id' => 'checkbox_field',
                            'type' => 'checkbox',
                        ]
                    ],
                ],
            ],
        ],
        'settings' => [
            'send_to' => 'test@test.com',
            'sender_name' => 'OpnForm',
            'subject' => 'New form submission with checkbox logic',
            'email_content' => 'Checkbox logic triggered.',
            'include_submission_data' => true,
            'include_hidden_fields_submission_data' => false,
            'reply_to' => null
        ]
    ];

    $this->postJson(route('open.forms.integrations.create', $form), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was created.'
        ]);
});
