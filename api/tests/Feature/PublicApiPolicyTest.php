<?php

use App\Models\Forms\Form;
use App\Models\User;
use App\Models\Workspace;
use Laravel\Sanctum\Sanctum;

describe('Public API Policy Tests', function () {
    // Test Pro Plan Policy
    it('not allowed to access the api with a non-pro user', function () {
        // Arrange
        $user = $this->createUser();
        $workspace = $this->createUserWorkspace($user);
        $this->createForm($user, $workspace);
        $form = $this->makeForm($user, $workspace);

        // Act & Assert: Add 'workspaces-read' to pass the FormController's workspace authorization check.
        Sanctum::actingAs($user, ['forms-read']);

        $this->getJson(route('open.forms.index-all'))
            ->assertUnauthorized();
    });

    // Test Form Policies
    it('allows read-only token to list forms but not create them', function () {
        // Arrange
        $user = $this->createProUser();
        $workspace = $this->createUserWorkspace($user);
        $this->createForm($user, $workspace);
        $form = $this->makeForm($user, $workspace);
        $formData = new \App\Http\Resources\FormResource($form);

        // Act & Assert: Add 'workspaces-read' to pass the FormController's workspace authorization check.
        Sanctum::actingAs($user, ['forms-read']);

        $this->getJson(route('open.forms.index-all'))
            ->assertSuccessful();

        $this->postJson(route('open.forms.store'), $formData->toArray(request()))
            ->assertForbidden();
    });

    it('allows write token to create a form', function () {
        // Arrange
        $user = $this->createProUser();
        $workspace = $this->createUserWorkspace($user);
        $form = $this->makeForm($user, $workspace);

        // Act & Assert
        Sanctum::actingAs($user, ['forms-write']);

        $formData = new \App\Http\Resources\FormResource($form);
        $this->postJson(route('open.forms.store'), $formData->toArray(request()))
            ->assertSuccessful();
    });

    // Test Submission Policies (now governed by Form abilities)
    it('allows forms-read token to list submissions', function () {
        // Arrange
        $user = $this->createProUser();
        $workspace = $this->createUserWorkspace($user);
        $form = $this->createForm($user, $workspace);
        $form->submissions()->create(['data' => []]);

        // Act & Assert
        Sanctum::actingAs($user, ['forms-read']);

        $this->getJson(route('open.forms.submissions.index', ['id' => $form->id]))
            ->assertSuccessful();
    });

    it('forbids forms-read token from deleting submissions', function () {
        // Arrange
        $user = $this->createProUser();
        $workspace = $this->createUserWorkspace($user);
        $form = $this->createForm($user, $workspace);
        $submission = $form->submissions()->create(['data' => []]);

        // Act & Assert
        Sanctum::actingAs($user, ['forms-read']);

        $this->deleteJson(route('open.forms.submissions.destroy', ['id' => $form->id, 'submission_id' => $submission->id]))
            ->assertForbidden();
    });

    it('allows forms-write token to delete a submission', function () {
        // Arrange
        $user = $this->createProUser();
        $workspace = $this->createUserWorkspace($user);
        $form = $this->createForm($user, $workspace);
        $submission = $form->submissions()->create(['data' => []]);

        // Act & Assert
        Sanctum::actingAs($user, ['forms-write']);

        $this->deleteJson(route('open.forms.submissions.destroy', ['id' => $form->id, 'submission_id' => $submission->id]))
            ->assertSuccessful();
    });

    // Test Cross-User Authorization
    it('forbids a token from accessing the resources of another user', function () {
        // Arrange
        $userA = $this->createProUser();
        $userB = $this->createProUser();
        $workspaceB = $this->createUserWorkspace($userB);
        $formB = $this->createForm($userB, $workspaceB);

        // Act
        Sanctum::actingAs($userA, ['forms-read']);

        // Assert: Laravel will return a 401 when a policy check fails on a model binding.
        $this->getJson(route('open.forms.show', ['slug' => $formB->slug]))
            ->assertUnauthorized();

        $this->getJson(route('open.forms.submissions.index', ['id' => $formB->id]))
            ->assertUnauthorized();
    });
});
