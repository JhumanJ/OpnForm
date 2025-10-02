<?php

use App\Models\User;

it('can login to Forms', function () {
    $user = User::factory()->create();

    $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'Abcd@1234',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['token', 'expires_in'])
        ->assertJson(['token_type' => 'bearer']);
});

it('can fetch current user', function () {
    $this->actingAs(User::factory()->create())
        ->getJson('/user')
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);
});

it('can log out', function () {
    $this->postJson('/login', [
        'email' => User::factory()->create()->email,
        'password' => 'Abcd@1234',
    ])->assertSuccessful();

    $this->assertAuthenticated();
    $this->postJson('/logout')
        ->assertSuccessful();

    $this->assertGuest();
    $this->getJson('/user')
        ->assertStatus(401);
});

it('cannot login if user is blocked', function () {
    $user = User::factory()->create([
        'blocked_at' => now(),
    ]);

    $this->postJson('/login', [
        'email' => $user->email,
        'password' => 'Abcd@1234',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email' => 'Your account has been blocked. Please contact support.']);

    $this->assertGuest();
});
