<?php

use App\Models\User;

use function Pest\Faker\faker;

it('can register', function () {
    $this->postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@test.app',
    ]);
});

it('cannot register with existing email', function () {
    User::factory()->create(['email' => 'test@test.app']);

    $this->postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'password' => 'secret',
        'password_confirmation' => 'secret',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('cannot register with disposable email', function () {
    // Select random email
    $email = faker()->randomElement([
        'dumliyupse@gufum.com',
        'kcs79722@zslsz.com',
        'pfizexwxtdifxupdhr@tpwlb.com',
        'qvj86ypqfm@email.edu.pl',
    ]);

    $this->postJson('/register', [
        'name' => 'Test disposable',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email'])
        ->assertJson([
            'message' => 'Disposable email addresses are not allowed.',
            'errors' => [
                'email' => [
                    'Disposable email addresses are not allowed.',
                ],
            ],
        ]);
});
