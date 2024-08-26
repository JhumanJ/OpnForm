<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('update profile info', function () {
    $this->actingAs($user = User::factory()->create())
        ->patchJson('/settings/profile', [
            'name' => 'Test User',
            'email' => 'test@test.app',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Test User',
        'email' => 'test@test.app',
    ]);
});

it('update password', function () {
    $this->actingAs($user = User::factory()->create())
        ->patchJson('/settings/password', [
            'password' => 'updated',
            'password_confirmation' => 'updated',
        ])
        ->assertSuccessful();

    $this->assertTrue(Hash::check('updated', $user->password));
});
