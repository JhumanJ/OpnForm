<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

test('validate auth', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    get(route('zapier.validate'))
        ->assertOk()
        ->assertJson([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

test('cannot validate auth with incorrect credentials', function () {
    get(route('zapier.validate'))
        ->assertUnauthorized();
});
