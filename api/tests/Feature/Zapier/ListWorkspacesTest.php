<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

test('list all workspaces of a user', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    $anotherUser = User::factory()->create();
    $anotherWorkspace = createUserWorkspace($anotherUser);

    Sanctum::actingAs(
        $user,
        ['list-workspaces']
    );

    get(route('zapier.workspaces'))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJson([
            [
                'id' => $workspace->id,
                'name' => $workspace->name,
            ]
        ]);
});

test('cannot list workspaces without a corresponding ability', function () {
    $user = User::factory()->create();
    $workspace = createUserWorkspace($user);

    Sanctum::actingAs($user);

    get(route('zapier.workspaces'))
        ->assertForbidden();
});
