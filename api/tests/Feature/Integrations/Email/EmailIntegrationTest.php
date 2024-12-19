<?php

use App\Models\Integration\FormIntegration;

test('free user can create one email integration', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // First email integration should succeed
    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'test@example.com',
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();
    expect(FormIntegration::where('form_id', $form->id)->count())->toBe(1);

    // Second email integration should fail
    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'another@example.com',
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                'settings.send_to' => ['Free users are limited to 1 email integration per form.']
            ]
        ]);
});

test('pro user can create multiple email integrations', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // First email integration
    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'test@example.com',
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();

    // Second email integration should also succeed for pro users
    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'another@example.com',
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();
    expect(FormIntegration::where('form_id', $form->id)->count())->toBe(2);
});

test('free user cannot add multiple emails', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => "test@example.com\nanother@example.com",
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['settings.send_to'])
        ->assertJson([
            'errors' => [
                'settings.send_to' => ['You can only send to a single email address on the free plan. Please upgrade to the Pro plan to create a new integration.']
            ]
        ]);
});

test('pro user can add multiple emails', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => "test@example.com\nanother@example.com\nthird@example.com",
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();

    $integration = FormIntegration::where('form_id', $form->id)->first();
    expect($integration)->not->toBeNull();
    expect($integration->data->send_to)->toContain('test@example.com');
    expect($integration->data->send_to)->toContain('another@example.com');
    expect($integration->data->send_to)->toContain('third@example.com');
});

test('free user can update their single email integration', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // Create initial integration
    $response = $this->postJson(route('open.forms.integration.create', $form), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'test@example.com',
            'sender_name' => 'Test Sender',
            'subject' => 'Test Subject',
            'email_content' => 'Test Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();
    $integrationId = $response->json('form_integration.id');

    // Update the integration
    $response = $this->putJson(route('open.forms.integration.update', [$form, $integrationId]), [
        'integration_id' => 'email',
        'status' => true,
        'settings' => [
            'send_to' => 'updated@example.com',
            'sender_name' => 'Updated Sender',
            'subject' => 'Updated Subject',
            'email_content' => 'Updated Content',
            'include_submission_data' => true
        ]
    ]);

    $response->assertSuccessful();

    $integration = FormIntegration::find($integrationId);
    expect($integration->data->send_to)->toBe('updated@example.com');
    expect($integration->data->sender_name)->toBe('Updated Sender');
});
