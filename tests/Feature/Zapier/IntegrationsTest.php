<?php

use App\Models\Integration\FormIntegration;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function PHPUnit\Framework\assertEquals;

test('create an integration', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form = createForm($user, $workspace, ['title' => 'First form']);

    Sanctum::actingAs(
        $user,
        ['manage-integrations']
    );

    post(route('zapier.webhooks.store'), [
        'form_id' => $form->id,
        'hookUrl' => $hookUrl = 'https://zapier.com/hook/test'
    ])
        ->assertOk();

    assertDatabaseCount('form_integrations', 1);

    $integration = FormIntegration::first();

    assertEquals($form->id, $integration->form_id);
    assertEquals($hookUrl, $integration->data->hook_url);
});

test('cannot create an integration without a corresponding ability', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form = createForm($user, $workspace, ['title' => 'First form']);

    Sanctum::actingAs($user);

    post(route('zapier.webhooks.store'), [
        'form_id' => $form->id,
        'hookUrl' => 'https://zapier.com/hook/test'
    ])
        ->assertForbidden();

    assertDatabaseCount('form_integrations', 0);
});

test('cannot create an integration for other users form', function () {
    $user = User::factory()->create();

    $user2 = User::factory()->create();
    $workspace = createUserWorkspace($user2);

    $form = createForm($user2, $workspace, ['title' => 'First form']);

    Sanctum::actingAs($user);

    post(route('zapier.webhooks.store'), [
        'form_id' => $form->id,
        'hookUrl' => 'https://zapier.com/hook/test'
    ])
        ->assertForbidden();

    assertDatabaseCount('form_integrations', 0);
});

test('delete an integration', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form = createForm($user, $workspace, ['title' => 'First form']);

    Sanctum::actingAs(
        $user,
        ['manage-integrations']
    );

    $integration = FormIntegration::factory()
        ->for($form)
        ->create([
            'data' => [
                'hook_url' => $hookUrl = 'https://zapier.com/hook/test'
            ]
        ]);

    assertDatabaseCount('form_integrations', 1);

    delete(route('zapier.webhooks.destroy', $integration), [
        'form_id' => $form->id,
        'hookUrl' => $hookUrl,
    ])
        ->assertOk();

    assertDatabaseCount('form_integrations', 0);
});

test('cannot delete an integration with an incorrect hook url', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form = createForm($user, $workspace, ['title' => 'First form']);

    Sanctum::actingAs(
        $user,
        ['manage-integrations']
    );

    $integration = FormIntegration::factory()
        ->for($form)
        ->create([
            'data' => [
                'hook_url' => 'https://zapier.com/hook/test'
            ]
        ]);

    delete(route('zapier.webhooks.destroy', $integration), [
        'form_id' => $form->id,
        'hookUrl' => 'https://google.com',
    ])
        ->assertOk();

    assertDatabaseCount('form_integrations', 1);
});
