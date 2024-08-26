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
            'notification_emails' => 'test@test.com',
            'notification_reply_to' => null
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
