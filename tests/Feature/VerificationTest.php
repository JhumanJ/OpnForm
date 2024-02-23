<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

it('can verify email', function () {
    $user = User::factory()->create(['email_verified_at' => null]);
    $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['user' => $user->id]);

    Event::fake();

    $this->postJson($url)
        ->assertSuccessful()
        ->assertJsonFragment(['status' => 'Your email has been verified!']);

    Event::assertDispatched(Verified::class, function (Verified $e) use ($user) {
        return $e->user->is($user);
    });
});
