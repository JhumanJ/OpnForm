<?php

use Illuminate\Support\Str;

it('can hide branding on upgrade', function () {
    $user = $this->actingAsUser();
    // Create workspaces and forms
    for ($i = 0; $i < 3; $i++) {
        $workspace = $this->createUserWorkspace($user);
        for ($j = 0; $j < 3; $j++) {
            $this->createForm($user, $workspace);
        }
    }

    // Forms don't have branding removed when created
    $forms = $user->workspaces()->with('forms')->get()->pluck('forms')->flatten();
    $forms->each(function ($form) {
        $this->assertEquals($form->no_branding, false);
    });

    // User subscribes
    $user->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => Str::random(),
        'stripe_status' => 'active',
        'stripe_price' => Str::random(),
        'quantity' => 1,
    ]);

    // Forms have branding removed after subscription
    $forms = $user->workspaces()->with('forms')->get()->pluck('forms')->flatten();
    $forms->each(function ($form) {
        $this->assertEquals($form->no_branding, true);
    });
});
