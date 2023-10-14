<?php

uses(\Tests\TestCase::class);

use function Pest\Faker\faker;
use Tests\Helpers\FormSubmissionDataFactory;

it('can create pro user who are subscribed', function () {
    $user = $this->actingAsProUser();
    expect($user->is_subscribed)->toBeTrue();
});

it('can create test workspace', function () {
    $user = $this->actingAsProUser();
    $this->createUserWorkspace($user);
    expect($user->workspaces()->count())->toBe(1);
});

it('can make a form for a database', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->makeForm($user,$workspace);
    expect($form->title)->not()->toBeNull();
    expect($form->description)->not()->toBeNull();
    expect(count($form->properties))->not()->toBe(0);
});
