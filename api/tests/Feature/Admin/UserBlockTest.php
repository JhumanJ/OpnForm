<?php

namespace Tests\Feature\Admin;

use App\Mail\UserBlockedEmail;
use App\Mail\UserUnblockedEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

function setupUsers()
{
    Config::set('opnform.moderator_emails', ['example@moderator.com']);
    $moderator = User::factory()->create([
        'email' => 'example@moderator.com',
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
    expect($user->blocked_at)->not->toBeNull();
    $lastBlock = $user->getLastBlock();
    expect($lastBlock['reason'])->toBe('Test block reason');
    expect($lastBlock['blocked_by'])->toBe($moderator->id);
    expect($lastBlock['blocked_at'])->not->toBeNull();
    expect($lastBlock['unblocked_at'])->toBeNull();

    Mail::assertSent(UserBlockedEmail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('can unblock a user', function () {
    Mail::fake();
    [$moderator, $user] = setupUsers();
    $user->blockUser('Initial reason', $moderator->id);
    $user->refresh();
    expect($user->is_blocked)->toBeTrue();


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
    expect($user->blocked_at)->toBeNull();
    $lastBlock = $user->getLastBlock();
    expect($lastBlock['unblock_reason'])->toBe('Test unblock reason');
    expect($lastBlock['unblocked_by'])->toBe($moderator->id);
    expect($lastBlock['unblocked_at'])->not->toBeNull();

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
