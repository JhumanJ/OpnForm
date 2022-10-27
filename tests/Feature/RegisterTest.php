<?php

use App\Models\User;
use Tests\TestCase;

it('can register', function () {
    $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@test.app'
    ]);
});

it('cannot register with existing email', function () {
    User::factory()->create(['email' => 'test@test.app']);

    $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'password' => 'secret',
        'password_confirmation' => 'secret',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
