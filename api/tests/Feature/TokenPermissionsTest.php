<?php

use App\Models\Workspace;
use Laravel\Sanctum\Sanctum;

it('can not create token with a non-pro user', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    $this->postJson(route('settings.tokens.store'), [
        'name' => 'New Token',
        'abilities' => ['forms-read'],
    ])->assertStatus(402);
});

it('can create token with a pro user', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);

    $this->postJson(route('settings.tokens.store'), [
        'name' => 'New Token',
        'abilities' => ['forms-read'],
    ])->assertSuccessful();
});


it('can access read-only workspace endpoints with read token', function () {
    $user = $this->createProUser();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user, ['role' => 'admin']);

    Sanctum::actingAs($user, ['workspaces-read']);

    $this->getJson(route('open.workspaces.index'))
        ->assertSuccessful();

    $this->postJson(route('open.workspaces.create'), [
        'name' => 'New Workspace',
        'icon' => '🚀',
    ])->assertForbidden();
});

it('can access write workspace endpoints with write token', function () {
    $user = $this->createProUser();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user, ['role' => 'admin']);

    Sanctum::actingAs($user, ['workspaces-write']);

    $this->postJson(route('open.workspaces.create'), [
        'name' => 'New Workspace',
        'icon' => '🚀',
    ])->assertSuccessful();
});
