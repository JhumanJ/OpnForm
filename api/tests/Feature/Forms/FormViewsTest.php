<?php

it('can see form without counting view for form owner', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson(function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($form) {
            return $json->where('id', $form->id)
                ->where('title', $form->title)
                ->whereType('properties', 'array')
                ->etc();
        });

    expect($form->views()->count())->toBe(0);
});

it('can see form and count view for guest', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);

    $this->actingAsGuest();

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson(function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($form) {
            return $json->where('id', $form->id)
                ->where('title', $form->title)
                ->whereType('properties', 'array')
                ->etc();
        });

    expect($form->views()->count())->toBe(1);
});
