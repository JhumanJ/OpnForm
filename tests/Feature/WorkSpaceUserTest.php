<?php

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserInvitationEmail;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->workspace = Workspace::factory()->create();
    $this->workspace->users()->attach($this->user, ['role' => 'admin']);
});

it('can list users in a workspace', function () {
    $this->actingAs($this->user)
        ->getJson(route('open.workspaces.users.index', ['workspaceId' => $this->workspace->id]))
        ->assertSuccessful()
        ->assertJsonCount(1);
});

it('can add a user to a workspace', function () {
    $newUser = User::factory()->create(['email' => 'newuser@example.com']);

    $this->actingAs($this->user)
        ->postJson(route('open.workspaces.users.add', ['workspaceId' => $this->workspace->id]), [
            'email' => $newUser->email,
            'role' => 'user',
        ])
        ->assertSuccessful()
        ->assertJson([
            'message' => 'User has been successfully added to workspace.'
        ]);

    expect($this->workspace->users()->count())->toBe(2);
});

it('can send an invitation email to a non-existing user', function () {
    Mail::fake();

    $this->actingAs($this->user)
        ->postJson(route('open.workspaces.users.add', ['workspaceId' => $this->workspace->id]), [
            'email' => 'nonexisting@example.com',
            'role' => 'user',
        ])
        ->assertSuccessful()
        ->assertJson([
            'message' => 'Registration invitation email sent to user.'
        ]);

    Mail::assertQueued(UserInvitationEmail::class);
});

it('can update user role in a workspace', function () {
    $existingUser = User::factory()->create();
    $this->workspace->users()->attach($existingUser, ['role' => 'user']);

    $this->actingAs($this->user)
        ->putJson(route('open.workspaces.users.update-role', [
            'workspaceId' => $this->workspace->id,
            'userId' => $existingUser->id
        ]), [
            'role' => 'admin',
        ])
        ->assertSuccessful()
        ->assertJson([
            'message' => 'User role changed successfully.'
        ]);
});

it('can remove a user from a workspace', function () {
    $existingUser = User::factory()->create();
    $this->workspace->users()->attach($existingUser);

    $this->actingAs($this->user)
        ->deleteJson(route('open.workspaces.users.remove', [
            'workspaceId' => $this->workspace->id,
            'userId' => $existingUser->id
        ]))
        ->assertSuccessful()
        ->assertJson([
            'message' => 'User removed from workspace successfully.'
        ]);

    expect($this->workspace->users()->count())->toBe(1);
});

it('can leave a workspace', function () {
    $this->actingAs($this->user)
        ->postJson(route('open.workspaces.leave', ['workspaceId' => $this->workspace->id]))
        ->assertSuccessful()
        ->assertJson([
            'message' => 'You have left the workspace successfully.'
        ]);

    expect($this->workspace->users()->count())->toBe(0);
});
