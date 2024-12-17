<?php

use App\Models\User;
use App\Rules\ValidHCaptcha;
use Illuminate\Support\Facades\Http;

it('can register', function () {

    Http::fake([
        ValidHCaptcha::H_CAPTCHA_VERIFY_URL => Http::response(['success' => true])
    ]);

    $this->postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'h-captcha-response' => 'test-token', // Mock token for testing
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'name', 'email']);

    $user = User::where('email', 'test@test.app')->first();
    expect($user)->not->toBeNull();
    expect($user->meta)->toHaveKey('registration_ip');
    expect($user->meta['registration_ip'])->toBe(request()->ip());
});

it('cannot register with existing email', function () {
    User::factory()->create(['email' => 'test@test.app']);

    $this->postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'h-captcha-response' => 'test-token',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('cannot register with disposable email', function () {
    Http::fake([
        ValidHCaptcha::H_CAPTCHA_VERIFY_URL => Http::response(['success' => true])
    ]);

    // Select random email
    $email = [
        'dumliyupse@gufum.com',
        'kcs79722@zslsz.com',
        'pfizexwxtdifxupdhr@tpwlb.com',
        'qvj86ypqfm@email.edu.pl',
    ][rand(0, 3)];

    $this->postJson('/register', [
        'name' => 'Test disposable',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'h-captcha-response' => 'test-token',
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

it('requires hcaptcha token when register', function () {
    $this->postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@test.app',
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['h-captcha-response']);
});
