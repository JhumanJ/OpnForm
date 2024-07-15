<?php

use App\Models\UserInvite;
use Carbon\Carbon;

it('can register with invite token', function () {
    $this->withoutExceptionHandling();
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $email = 'invitee@gmail.com';
    $inviteData = ['email' => $email, 'role' => 'user'];
    $this->postJson(route('open.workspaces.users.add', $workspace->id), $inviteData)
        ->assertSuccessful();

    expect($workspace->invites()->count())->toBe(1);
    $userInvite = UserInvite::latest()->first();
    $token = $userInvite->token;

    $this->postJson('/logout')
        ->assertSuccessful();

    // Register with token
    $response = $this->postJson('/register', [
        'name' => 'Invitee',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'invite_token' => $token,
    ]);
    $response->assertSuccessful();
    expect($workspace->users()->count())->toBe(2);
});

it('cannot register with expired invite token', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $email = 'invitee@gmail.com';
    $inviteData = ['email' => $email, 'role' => 'user'];
    $this->postJson(route('open.workspaces.users.add', $workspace->id), $inviteData)
        ->assertSuccessful();

    expect($workspace->invites()->count())->toBe(1);
    $userInvite = UserInvite::latest()->first();
    $token = $userInvite->token;

    $this->postJson('/logout')
        ->assertSuccessful();

    Carbon::setTestNow(now()->addDays(8));
    // Register with token
    $response = $this->postJson('/register', [
        'name' => 'Invitee',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'invite_token' => $token,
    ]);
    $response->assertStatus(400)->assertJson([
        'message' => 'Invite token has expired.',
    ]);
    expect($workspace->users()->count())->toBe(1);
});

it('cannot re-register with accepted invite token', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $email = 'invitee@gmail.com';
    $inviteData = ['email' => $email, 'role' => 'user'];
    $this->postJson(route('open.workspaces.users.add', $workspace->id), $inviteData)
        ->assertSuccessful();

    expect($workspace->invites()->count())->toBe(1);
    $userInvite = UserInvite::latest()->first();
    $token = $userInvite->token;

    $this->postJson('/logout')
        ->assertSuccessful();

    // Register with token
    $response = $this->postJson('/register', [
        'name' => 'Invitee',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'invite_token' => $token,
    ]);
    $response->assertSuccessful();
    expect($workspace->users()->count())->toBe(2);

    $this->postJson('/logout')
    ->assertSuccessful();

    // Register again with same used token
    $response = $this->postJson('/register', [
        'name' => 'Invitee',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'invite_token' => $token,
    ]);

    $response->assertStatus(422)->assertJson([
        'message' => 'The email has already been taken.',
    ]);
    expect($workspace->users()->count())->toBe(2);
});

it('can cancel user invite', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $email = 'invitee@gmail.com';
    $inviteData = ['email' => $email, 'role' => 'user'];
    $response = $this->postJson(route('open.workspaces.users.add', $workspace->id), $inviteData)
        ->assertSuccessful();

    expect($workspace->invites()->count())->toBe(1);
    $userInvite = UserInvite::latest()->first();
    $token = $userInvite->token;

    // Cancel the invite
    $this->deleteJson(route('open.workspaces.invites.cancel', ['workspaceId' => $workspace->id, 'inviteId' => $userInvite->id]))
        ->assertSuccessful();

    $this->postJson('/logout')
        ->assertSuccessful();

    // Register with token
    $response = $this->postJson('/register', [
        'name' => 'Invitee',
        'email' => $email,
        'hear_about_us' => 'google',
        'password' => 'secret',
        'password_confirmation' => 'secret',
        'agree_terms' => true,
        'invite_token' => $token,
    ]);
    $response->assertStatus(400)->assertJson([
        'message' => 'Invite token is invalid.',
    ]);

    expect($workspace->users()->count())->toBe(1);
});
