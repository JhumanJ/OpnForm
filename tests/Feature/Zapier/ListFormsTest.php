<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

test('list all forms of a given workspace', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form1 = createForm($user, $workspace, ['title' => 'First form']);
    $form2 = createForm($user, $workspace, ['title' => 'Second form']);

    Sanctum::actingAs(
        $user,
        ['list-forms']
    );

    get(route('zapier.forms', ['workspace_id' => $workspace->id]))
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJson([
            [
                'id' => $form1->id,
                'name' => $form1->title,
            ],
            [
                'id' => $form2->id,
                'name' => $form2->title,
            ],
        ]);
});

test('cannot list forms without a corresponding ability', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $form1 = createForm($user, $workspace, ['title' => 'First form']);
    $form2 = createForm($user, $workspace, ['title' => 'Second form']);

    Sanctum::actingAs($user);

    get(route('zapier.forms', ['workspace_id' => $workspace->id]))
        ->assertForbidden();
});

test('cannot other users forms', function () {
    $user = User::factory()->create();

    $user2 = User::factory()->create();
    $workspace = createUserWorkspace($user2);

    $form1 = createForm($user, $workspace, ['title' => 'First form']);
    $form2 = createForm($user, $workspace, ['title' => 'Second form']);

    Sanctum::actingAs($user);

    get(route('zapier.forms', ['workspace_id' => $workspace->id]))
        ->assertForbidden();
});
