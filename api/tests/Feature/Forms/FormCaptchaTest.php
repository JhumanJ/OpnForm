<?php

it('create form with captcha and raise validation issue', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'use_captcha' => true,
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
