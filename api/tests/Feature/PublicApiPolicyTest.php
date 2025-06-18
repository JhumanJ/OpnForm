<?php

use App\Models\Forms\Form;
use App\Models\User;
use App\Models\Workspace;
use Laravel\Sanctum\Sanctum;

describe('Public API Policy Tests', function () {
    // Test Form Policies
    it('allows read-only token to list forms but not create them', function () {
        // Arrange: Create a user, workspace, and a form.
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user, ['role' => 'admin']);
        Form::factory()->for($workspace, 'workspace')->create([
            'creator_id' => $user->id,
            'properties' => [],
        ]);

        // Act as the user via Sanctum with only 'forms-read' ability.
        Sanctum::actingAs($user, ['forms-read']);

        // Assert: The user can list all forms.
        $this->getJson(route('open.forms.index-all'))
            ->assertSuccessful();

        // Assert: The user is forbidden from creating a form.
        $this->postJson(route('open.forms.store'), [
            'workspace_id' => $workspace->id,
            'title' => 'New Form',
            'properties' => [],
        ])->assertForbidden();
    });

    it('allows write token to create a form', function () {
        // Arrange
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user, ['role' => 'admin']);

        // Act with 'forms-write' ability
        Sanctum::actingAs($user, ['forms-write']);

        // Assert: The user can create a form.
        $this->postJson(route('open.forms.store'), [
            'workspace_id' => $workspace->id,
            'title' => 'New Form from API',
            'properties' => [],
            'visibility' => 'public',
            'language' => 'en',
            'theme' => 'default',
            'width' => 'full',
            'size' => 'md',
            'border_radius' => 'md',
            'dark_mode' => false,
            'color' => '#3B82F6',
            'uppercase_labels' => false,
            'no_branding' => false,
            'transparent_background' => false,
        ])->assertSuccessful();
    });

    // Test Submission Policies
    it('allows read-only token to list submissions but not delete them', function () {
        // Arrange
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user, ['role' => 'admin']);
        $form = Form::factory()->for($workspace, 'workspace')->create([
            'creator_id' => $user->id,
            'properties' => [],
        ]);
        $submission = $form->submissions()->create(['data' => []]);

        // Act with 'submissions-read' ability
        Sanctum::actingAs($user, ['submissions-read']);

        // Assert: Can list submissions
        $this->getJson(route('open.forms.submissions.index', ['id' => $form->id]))
            ->assertSuccessful();

        // Assert: Forbidden from deleting
        $this->deleteJson(route('open.forms.submissions.destroy', ['id' => $form->id, 'submission_id' => $submission->id]))
            ->assertForbidden();
    });

    it('allows write token to delete a submission', function () {
        // Arrange
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create();
        $workspace->users()->attach($user, ['role' => 'admin']);
        $form = Form::factory()->for($workspace, 'workspace')->create([
            'creator_id' => $user->id,
            'properties' => [],
        ]);
        $submission = $form->submissions()->create(['data' => []]);

        // Act with 'submissions-write' ability
        Sanctum::actingAs($user, ['submissions-write']);

        // Assert: Can delete a submission
        $this->deleteJson(route('open.forms.submissions.destroy', ['id' => $form->id, 'submission_id' => $submission->id]))
            ->assertSuccessful();
    });
});
