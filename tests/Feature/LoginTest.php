<?php

use App\Models\User;

it('can login to Forms', function () {
    $user = User::factory()->create();

    $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['token', 'expires_in'])
        ->assertJson(['token_type' => 'bearer']);
});

it('can fetch current user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/api/user')
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);
});

it('can log out', function () {
    $this->postJson('/api/login', [
        'email' => User::factory()->create()->email,
        'password' => 'password',
    ])->assertSuccessful();

    $this->assertAuthenticated();
    $this->postJson("/api/logout")
        ->assertSuccessful();

    $this->assertGuest();
    $this->getJson("/api/user")
        ->assertStatus(401);
});

