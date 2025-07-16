<?php

namespace Tests\Feature\Admin;

use App\Mail\UserBlockedEmail;
use App\Mail\UserUnblockedEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function setupUsers()
{
    $moderator = User::factory()->create([
        'email' => 'julien@notionforms.io',
    ]);

    $user = User::factory()->create();

    return [$moderator, $user];
}

it('can block a user', function () {
    Mail::fake();
    [$moderator, $user] = setupUsers();

    $this->actingAs($moderator)
        ->postJson('/moderator/block-user', [
            'user_id' => $user->id,
            'reason' => 'Test block reason',
        ])
        ->assertSuccessful()
        ->assertJson([
            'message' => 'User has been blocked.',
        ]);

    $user->refresh();
    expect($user->is_blocked)->toBeTrue();
    expect($user->meta['block_reason'])->toBe('Test block reason');
    expect($user->meta['blocked_by'])->toBe($moderator->id);

    Mail::assertSent(UserBlockedEmail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('can unblock a user', function () {
    Mail::fake();
    [$moderator, $user] = setupUsers();
    $user->update([
        'meta' => ['blocked_at' => now(), 'block_reason' => 'Initial reason'],
    ]);

    $this->actingAs($moderator)
        ->postJson('/moderator/unblock-user', [
            'user_id' => $user->id,
            'reason' => 'Test unblock reason',
        ])
        ->assertSuccessful()
        ->assertJson([
            'message' => 'User has been unblocked.',
        ]);

    $user->refresh();
    expect($user->is_blocked)->toBeFalse();
    expect($user->meta)->not->toHaveKey('block_reason');
    expect($user->meta['unblock_reason'])->toBe('Test unblock reason');

    Mail::assertSent(UserUnblockedEmail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('does not allow a non-moderator to block a user', function () {
    $nonModerator = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($nonModerator)
        ->postJson('/moderator/block-user', [
            'user_id' => $user->id,
            'reason' => 'Attempting to block',
        ])
        ->assertForbidden();
});
