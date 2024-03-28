<?php

it('can fetch form integration events', function () {
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

    $this->getJson(route('open.forms.integrations.events', [$form->id, $response->json('form_integration.id')]))
        ->assertSuccessful()
        ->assertJsonCount(0);
});
