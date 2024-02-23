<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Helpers\FormSubmissionDataFactory;

beforeEach(function () {
    $this->password = '12345';
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $this->form = $this->createForm($user, $workspace, [
        'password' => $this->password,
    ]);
    $this->formData = FormSubmissionDataFactory::generateSubmissionData($this->form);
});

it('can allow form owner to access and submit form without password', function () {
    // As Form Owner so can access form without password
    $this->getJson(route('forms.show', $this->form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            return $json->where('id', $this->form->id)
                ->where('has_password', true)
                ->where('is_password_protected', false)
                ->etc();
        });

    // As Form Owner so can submit form without password
    $this->postJson(route('forms.answer', $this->form->slug), $this->formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('can not access form without password for guest user', function () {
    $this->actingAsGuest();

    $this->getJson(route('forms.show', $this->form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            return $json->where('id', $this->form->id)
                ->where('has_password', true)
                ->where('is_password_protected', true)
                ->etc();
        });
});

it('can not submit form without password for guest user', function () {
    $this->actingAsGuest();

    $this->postJson(route('forms.answer', $this->form->slug), $this->formData)
        ->assertStatus(403)
        ->assertJson([
            'status' => 'Unauthorized',
            'message' => 'Form is protected.',
        ]);
});

it('can not submit form with wrong password for guest user', function () {
    $this->actingAsGuest();

    $this->withHeaders(['form-password' => hash('sha256', 'WRONGPASSWORD')])
        ->postJson(route('forms.answer', $this->form->slug), $this->formData)
        ->assertStatus(403)
        ->assertJson([
            'status' => 'Unauthorized',
            'message' => 'Form is protected.',
        ]);
});

it('can access form with right password for guest user', function () {
    $this->actingAsGuest();

    $this->withHeaders(['form-password' => hash('sha256', $this->password)])
        ->getJson(route('forms.show', $this->form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) {
            return $json->where('id', $this->form->id)
                ->where('has_password', true)
                ->where('is_password_protected', false)
                ->etc();
        });
});

it('can submit form with right password for guest user', function () {
    $this->actingAsGuest();

    $this->withHeaders(['form-password' => hash('sha256', $this->password)])
        ->postJson(route('forms.answer', $this->form->slug), $this->formData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});
