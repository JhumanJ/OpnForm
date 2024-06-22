<?php

use App\Models\User;

it('can login to Forms', function () {
    $user = User::factory()->create();

    $this->postJson('/oauth/token', [
        'username' => $user->email,
        'password' => 'password',
        'grant_type' => 'password',
        'scope' => '*',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['access_token', 'refresh_token', 'token_type', 'expires_in']);
});

it('can fetch current user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/user')
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);
});

it('can log out', function () {
    $this->actingAsUser(User::factory()->create());

    $this->assertAuthenticated();
    $this->postJson('/logout')
        ->assertSuccessful();
})->skip();
