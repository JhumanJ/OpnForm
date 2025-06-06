<?php

use Illuminate\Testing\Fluent\AssertableJson;

it('can create a contact form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);
    $formData = new \App\Http\Resources\FormResource($form);

    $response = $this->postJson(route('open.forms.store', $formData->toArray(request())))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form created.',
        ]);

    expect($workspace->forms()->count())->toBe(1);
    $this->assertDatabaseHas('forms', [
        'id' => $response->json('form.id'),
    ]);
});

it('can fetch forms', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->getJson(route('open.workspaces.forms.index', $workspace->id))
        ->assertSuccessful()
        ->assertJsonCount(3)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $form->id)
        ->assertJsonPath('data.0.title', $form->title);
});

it('can fetch a form', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $response = $this->getJson(route('open.forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson([
            'id' => $form->id,
            'title' => $form->title
        ]);
});

it('can update a form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $newFormData = $this->makeForm($user, $workspace);
    $form->fill((new \App\Http\Resources\FormResource($newFormData))->toArray(request()));

    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $this->putJson(route('open.forms.update', $form->id), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form updated.',
        ]);

    $this->assertDatabaseHas('forms', [
        'id' => $form->id,
        'title' => $form->title
    ]);
});

it('can regenerate a form url', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $newFormData = $this->makeForm($user, $workspace);
    $form->update([
        'title' => $newFormData->title,
    ]);
    $form->generateSlug();
    $newSlug = $form->slug;

    $this->putJson(route('open.forms.regenerate-link', [$form->id, 'uuid']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            return $json->where('type', 'success')
                ->where('form.slug', function ($value) {
                    if (!is_string($value) || (preg_match(
                        '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
                        $value
                    ) !== 1)) {
                        return false;
                    }

                    return true;
                })
                ->etc();
        });

    $this->putJson(route('open.forms.regenerate-link', [$form->id, 'slug']))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($newSlug) {
            return $json->where('type', 'success')
                ->where('form.slug', function ($slug) use ($newSlug) {
                    return substr($slug, 0, -6) == substr($newSlug, 0, -6);
                })
                ->etc();
        });
});

it('can duplicate a form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $response = $this->postJson(route('open.forms.duplicate', $form->id))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form successfully duplicated. You are now editing the duplicated version of the form.',
        ]);

    expect($user->forms()->count())->toBe(2);
    expect($workspace->forms()->count())->toBe(2);
    $this->assertDatabaseHas('forms', [
        'id' => $response->json('new_form.id'),
        'title' => 'Copy of ' . $form->title
    ]);
});

it('can delete a form', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->deleteJson(route('open.forms.destroy', $form->id))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form was deleted.',
        ]);
    expect($user->forms()->count())->toBe(0);
    expect($workspace->forms()->count())->toBe(0);
});

it('can create form with dark mode', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'dark_mode' => 'dark',
    ]);
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $this->postJson(route('open.forms.store', $formData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form created.',
        ]);

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($form) {
            return $json->where('id', $form->id)
                ->where('dark_mode', 'dark')
                ->etc();
        });
});

it('can create form with custom scripts', function () {
    $user = $this->actingAsTrialingUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'custom_code' => "<script>console.log('Hello')</script>",
    ]);
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());
    $this->postJson(route('open.forms.store', $formData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form successfully created, but the Non-trial features you used will be disabled when sharing your form:',
            'form' => ['custom_code' => null]
        ]);

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()->assertJson([
            'id' => $form->id,
            'title' => $form->title,
            'custom_code'  => null
        ]);
})->skip(true, 'Trialing custom script form cleaning disabled for now.');

it('can not set custom slug when not self hosted', function () {
    config(['app.self_hosted' => false]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);
    $form->slug = 'my-custom-slug-123';
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $response = $this->postJson(route('open.forms.store', $formData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form created.'
        ]);
    $this->assertNotEquals($response->json('form.slug'), 'my-custom-slug-123');
});

it('can set custom slug when self hosted', function () {
    config(['app.self_hosted' => true]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);
    $form->slug = 'my-custom-slug-123';
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $response = $this->postJson(route('open.forms.store', $formData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form created.'
        ]);
    $this->assertEquals($response->json('form.slug'), 'my-custom-slug-123');
});

it('rejects invalid custom slug format when self hosted', function () {
    config(['app.self_hosted' => true]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);
    $form->slug = 'Invalid Slug!@#';
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $this->postJson(route('open.forms.store', $formData))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});

it('rejects duplicate custom slug when self hosted', function () {
    config(['app.self_hosted' => true]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    // Create first form with custom slug
    $form1 = $this->createForm($user, $workspace);
    $form1->update(['slug' => 'duplicate-slug']);

    // Try to create second form with same slug
    $form2 = $this->makeForm($user, $workspace);
    $form2->slug = $form1->slug;
    $formData = (new \App\Http\Resources\FormResource($form2))->toArray(request());

    $this->postJson(route('open.forms.store', $formData))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});

it('allows empty custom slug when self hosted', function () {
    config(['app.self_hosted' => true]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user, $workspace);
    $form->slug = null;
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $this->postJson(route('open.forms.store', $formData))
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form created.',
        ]);
});

it('can update form with custom slug when self hosted', function () {
    config(['app.self_hosted' => true]);

    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $form->slug = 'updated-custom-slug';
    $formData = (new \App\Http\Resources\FormResource($form))->toArray(request());

    $this->putJson(route('open.forms.update', $form->id), $formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form updated.',
        ]);

    $this->assertDatabaseHas('forms', [
        'id' => $form->id,
        'slug' => 'updated-custom-slug'
    ]);
});
