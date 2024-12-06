<?php

test('form accepts long redirect urls', function () {
    $this->withoutExceptionHandling();
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    // Create a very long URL (more than 255 characters)
    $longUrl = 'https://example.com/?' . str_repeat('very-long-parameter=value&', 50);

    $this->putJson(route('open.forms.update', $form->id), array_merge($form->toArray(), [
        'redirect_url' => $longUrl
    ]))->assertStatus(200);

    expect($form->fresh()->redirect_url)->toBe($longUrl);
});

test('form accepts null redirect url', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->putJson(route('open.forms.update', $form->id), array_merge($form->toArray(), [
        'redirect_url' => null
    ]))->assertStatus(200);

    expect($form->fresh()->redirect_url)->toBeNull();
});
