<?php

use App\Models\User;
use App\Models\Workspace;
use Laravel\Sanctum\Sanctum;

it('can access read-only workspace endpoints with read token', function () {
    $user = User::factory()->create();
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
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user, ['role' => 'admin']);

    Sanctum::actingAs($user, ['workspaces-write']);

    $this->postJson(route('open.workspaces.create'), [
        'name' => 'New Workspace',
        'icon' => '🚀',
    ])->assertSuccessful();
});
