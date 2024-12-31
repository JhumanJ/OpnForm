<?php

it('create form with hcaptcha and raise validation issue', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'use_captcha' => true,
        'captcha_provider' => 'hcaptcha',
    ]);

    $this->postJson(route('forms.answer', $form->slug), [])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Please complete the captcha.',
            'errors' => [
                'h-captcha-response' => [
                    'Please complete the captcha.',
                ],
            ],
        ]);
});

it('create form with recaptcha and raise validation issue', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'use_captcha' => true,
        'captcha_provider' => 'recaptcha',
    ]);

    $this->postJson(route('forms.answer', $form->slug), [])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Please complete the captcha.',
            'errors' => [
                'g-recaptcha-response' => [
                    'Please complete the captcha.',
                ],
            ],
        ]);
});
