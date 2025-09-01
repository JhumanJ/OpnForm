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

    $response = $this->postJson(route('open.forms.integration.create', $form), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was created.'
        ]);

    $this->getJson(route('open.forms.integrations', $form))
        ->assertSuccessful()
        ->assertJsonCount(1);

    $this->putJson(route('open.forms.integration.update', [$form, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was updated.'
        ]);

    $this->deleteJson(route('open.forms.integration.destroy', [$form, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was deleted.'
        ]);
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

    $this->postJson(route('open.forms.integration.create', $form), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was created.'
        ]);
});
