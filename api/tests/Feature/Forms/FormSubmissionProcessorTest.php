<?php

use App\Service\Forms\FormSubmissionProcessor;

it('processes synchronously with editable submissions', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'editable_submissions' => true
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeTrue();
});

it('processes synchronously with UUID field in redirect URL', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/{field_1}',
        'properties' => [
            [
                'id' => 'field_1',
                'type' => 'text',
                'generates_uuid' => true,
                'name' => 'UUID Field'
            ]
        ]
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeTrue();
});

it('processes synchronously with auto increment field in redirect URL', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/{field_1}',
        'properties' => [
            [
                'id' => 'field_1',
                'type' => 'text',
                'generates_auto_increment_id' => true,
                'name' => 'Auto Increment Field'
            ]
        ]
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeTrue();
});

it('processes asynchronously with no generated fields in redirect URL', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/{field_1}',
        'properties' => [
            [
                'id' => 'field_1',
                'type' => 'text',
                'name' => 'Regular Field'
            ]
        ]
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeFalse();
});

it('processes asynchronously when generated field is not used in redirect URL', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/{field_2}',
        'properties' => [
            [
                'id' => 'field_1',
                'type' => 'text',
                'generates_uuid' => true,
                'name' => 'UUID Field'
            ],
            [
                'id' => 'field_2',
                'type' => 'text',
                'name' => 'Regular Field'
            ]
        ]
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeFalse();
});

it('processes asynchronously with no redirect URL', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => null,
        'properties' => [
            [
                'id' => 'field_1',
                'type' => 'text',
                'generates_uuid' => true,
                'name' => 'UUID Field'
            ]
        ]
    ]);

    $processor = new FormSubmissionProcessor();
    expect($processor->shouldProcessSynchronously($form))->toBeFalse();
});

it('formats redirect data correctly for pro users', function () {
    $user = $this->actingAsProUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/<span mention mention-field-id="field_1"></span>'
    ]);

    $processor = new FormSubmissionProcessor();
    $redirectData = $processor->getRedirectData($form, [
        'field_1' => [
            'id' => 'field_1',
            'value' => 'test-value'
        ]
    ]);

    expect($redirectData)->toBe([
        'redirect' => true,
        'redirect_url' => 'https://example.com/test-value'
    ]);
});

it('returns no redirect for non-pro users', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'redirect_url' => 'https://example.com/<span mention mention-field-id="field_1"></span>'
    ]);

    $processor = new FormSubmissionProcessor();
    $redirectData = $processor->getRedirectData($form, [
        'field_1' => [
            'id' => 'field_1',
            'value' => 'test-value'
        ]
    ]);

    expect($redirectData)->toBe([
        'redirect' => false
    ]);
});
