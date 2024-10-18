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

    $response = $this->postJson(route('open.forms.integration.create', $form->id), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was created.'
        ]);

    $this->getJson(route('open.forms.integrations', $form->id))
        ->assertSuccessful()
        ->assertJsonCount(1);

    $this->putJson(route('open.forms.integration.update', [$form->id, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was updated.'
        ]);

    $this->deleteJson(route('open.forms.integration.destroy', [$form->id, $response->json('form_integration.id')]), $data)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form Integration was deleted.'
        ]);
});
