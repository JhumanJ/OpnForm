<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Helpers\FormSubmissionDataFactory;

it('create form with logic', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'title',
                'hidden' => false,
                'required' => true,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'email',
                                'value' => [
                                    'operator' => 'is_empty',
                                    'property_meta' => [
                                        'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee124',
                                        'type' => 'email',
                                    ],
                                    'value' => true,
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['make-it-optional'],
                ],
            ],
        ],
    ]);

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($form) {
            return $json->where('id', $form->id)
                ->where('properties', function ($values) {
                    return count($values[0]['logic']) > 0;
                })
                ->etc();
        });

    // Should submit form
    $forData = ['93ea3198-353f-440b-8dc9-2ac9a7bee124' => ''];
    $this->postJson(route('forms.answer', $form->slug), $forData)
        ->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('create form with multi select logic', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'title',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'multi_select',
                                'value' => [
                                    'operator' => 'contains',
                                    'property_meta' => [
                                        'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee124',
                                        'type' => 'multi_select',
                                    ],
                                    'value' => 'One',
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['require-answer'],
                ],
            ],
        ],
    ]);

    $this->getJson(route('forms.show', $form->slug))
        ->assertSuccessful()
        ->assertJson(function (AssertableJson $json) use ($form) {
            return $json->where('id', $form->id)
                ->where('properties', function ($values) {
                    return count($values[0]['logic']) > 0;
                })
                ->etc();
        });

    // Should submit form
    $forData = ['93ea3198-353f-440b-8dc9-2ac9a7bee124' => ['One']];
    $this->postJson(route('forms.answer', $form->slug), $forData)
        ->assertStatus(422)
        ->assertJson([
            'message' => 'The Name field is required.',
            'errors' => [
                'title' => [
                    'The Name field is required.',
                ],
            ],
        ]);
});

it('preserves multi-select values during validation with logic conditions', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    // Create a form with a multi-select field and a text field that has logic based on the multi-select
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => 'multi_select_field',
                'name' => 'Multi Select Field',
                'type' => 'multi_select',
                'hidden' => false,
                'required' => true,
                'multi_select' => [
                    'options' => [
                        ['id' => 'option1', 'name' => 'Option 1'],
                        ['id' => 'option2', 'name' => 'Option 2'],
                        ['id' => 'option3', 'name' => 'Option 3']
                    ]
                ]
            ],
            [
                'id' => 'text_field',
                'name' => 'Text Field',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'multi_select',
                                'value' => [
                                    'operator' => 'contains',
                                    'property_meta' => [
                                        'id' => 'multi_select_field',
                                        'type' => 'multi_select'
                                    ],
                                    'value' => 'Option 1'
                                ]
                            ]
                        ]
                    ],
                    'actions' => ['require-answer']
                ]
            ]
        ]
    ]);

    // Submit form data with multi-select values
    $formData = [
        'multi_select_field' => ['Option 1', 'Option 2']
    ];

    ray($formData)->blue('Original form data');

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);

    // The validation should fail because text_field is required when Option 1 is selected
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The Text Field field is required.',
            'errors' => [
                'text_field' => ['The Text Field field is required.']
            ]
        ]);

    // Check that the multi-select values were preserved in the validation data
    ray($response->json())->purple('Response data');
    expect($response->json('errors.multi_select_field'))->toBeNull();
});

it('correctly handles multi-select values with complex form logic', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    // Create form with the specific fields from your example
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                'name' => 'Which event would you like to join?',
                'type' => 'multi_select',
                'hidden' => false,
                'required' => true,
                'multi_select' => [
                    'options' => [
                        ['id' => 'Ashkelon Run (March 21)', 'name' => 'Ashkelon Run (March 21)'],
                        ['id' => 'Jerusalem Marathon (April 4)', 'name' => 'Jerusalem Marathon (April 4)'],
                        ['id' => 'Neither', 'name' => 'Neither']
                    ]
                ]
            ],
            [
                'id' => '0ca51469-6bda-40f4-831c-084f066643d7',
                'name' => 'Jerusalem Marathon - Run Options',
                'type' => 'select',
                'hidden' => true,
                'required' => false,
                'select' => [
                    'options' => [
                        ['id' => '10km (Most popular)', 'name' => '10km (Most popular)']
                    ]
                ],
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                                'value' => [
                                    'operator' => 'contains',
                                    'property_meta' => [
                                        'id' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                                        'type' => 'multi_select'
                                    ],
                                    'value' => 'Jerusalem Marathon (April 4)'
                                ]
                            ]
                        ]
                    ],
                    'actions' => ['require-answer', 'show-block']
                ]
            ]
        ]
    ]);

    // Submit form data matching your payload
    $formData = [
        '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b' => ['Jerusalem Marathon (April 4)'],
        '0ca51469-6bda-40f4-831c-084f066643d7' => '10km (Most popular)'
    ];

    ray($formData)->blue('Original form data');

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);

    ray($response->json())->purple('Response data');

    // Should be successful since all required fields are filled
    $response->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.'
        ]);

    // Now let's verify the saved submission data
    $submission = $form->submissions()->first();
    expect($submission->data['93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b'])->toBe(['Jerusalem Marathon (April 4)']);
});

it('preserves multi-select values when building validation rules', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);

    // Create form with the exact fields from your real form
    $form = $this->createForm($user, $workspace, [
        'properties' => [
            [
                'id' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                'name' => 'Which event would you like to join?',
                'type' => 'multi_select',
                'required' => true,
                'multi_select' => [
                    'options' => [
                        ['id' => 'Jerusalem Marathon (April 4)', 'name' => 'Jerusalem Marathon (April 4)'],
                        ['id' => 'Ashkelon Run (March 21)', 'name' => 'Ashkelon Run (March 21)']
                    ]
                ]
            ],
            [
                'id' => '72565901-c345-427a-b988-0ce3de9ad9f4',
                'name' => 'Additional Days',
                'type' => 'multi_select',
                'required' => false,
                'multi_select' => [
                    'options' => [
                        ['id' => 'Thursday', 'name' => 'Thursday'],
                        ['id' => 'Sunday', 'name' => 'Sunday']
                    ]
                ],
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                                'value' => [
                                    'operator' => 'contains',
                                    'property_meta' => [
                                        'id' => '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b',
                                        'type' => 'multi_select'
                                    ],
                                    'value' => 'Ashkelon Run (March 21)'
                                ]
                            ]
                        ]
                    ],
                    'actions' => ['require-answer']
                ]
            ]
        ]
    ]);

    // Submit form data with Jerusalem Marathon
    $formData = [
        '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b' => ['Jerusalem Marathon (April 4)']
    ];

    ray($formData)->blue('Original form data');

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);

    ray($response->json())->purple('Response data');

    // Should be successful since Jerusalem Marathon doesn't require Additional Days
    $response->assertSuccessful();

    // Now try with Ashkelon Run which requires Additional Days
    $formData = [
        '93c8ebe9-b1ba-42ce-841c-bf3b9be1ca4b' => ['Ashkelon Run (March 21)']
    ];

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);

    // Should fail because Additional Days is required when Ashkelon Run is selected
    $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                '72565901-c345-427a-b988-0ce3de9ad9f4' => ['The Additional Days field is required.']
            ]
        ]);
});


it('can submit form with passed regex validation condition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Email')->first();

    // Regex condition to check if email is from gmail.com domain
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'matches_regex',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'text',
                        ],
                        'value' => '^[a-zA-Z0-9._%+-]+@gmail\.com$',
                    ],
                ],
            ],
        ],
    ];

    $submissionData = [];
    $validationMessage = 'Must be a Gmail address';

    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, $targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 'test@gmail.com';
        }
        return $property;
    })->toArray();

    $form->update();
    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);
    $response->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('can not submit form with failed regex validation condition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Email')->first();

    // Regex condition to check if email is from gmail.com domain
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'matches_regex',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'text',
                        ],
                        'value' => '^[a-zA-Z0-9._%+-]+@gmail\.com$',
                    ],
                ],
            ],
        ],
    ];

    $submissionData = [];
    $validationMessage = 'Must be a Gmail address';

    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, $targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 'test@yahoo.com'; // Non-Gmail address should fail
        }
        return $property;
    })->toArray();

    $form->update();
    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => $validationMessage,
        ]);
});

it('can submit form with does not match regex validation condition', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Email')->first();

    // Regex condition to check if email is NOT from gmail.com domain
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'does_not_match_regex',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'text',
                        ],
                        'value' => '^[a-zA-Z0-9._%+-]+@gmail\.com$',
                    ],
                ],
            ],
        ],
    ];

    $submissionData = [];
    $validationMessage = 'Gmail addresses not allowed';

    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, $targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 'test@yahoo.com'; // Non-Gmail address should pass
        }
        return $property;
    })->toArray();

    $form->update();
    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $response = $this->postJson(route('forms.answer', $form->slug), $formData);
    $response->assertSuccessful()
        ->assertJson([
            'type' => 'success',
            'message' => 'Form submission saved.',
        ]);
});

it('handles invalid regex patterns gracefully', function () {
    $user = $this->actingAsUser();
    $workspace = $this->createUserWorkspace($user);
    $form = $this->createForm($user, $workspace);
    $targetField = collect($form->properties)->where('name', 'Email')->first();

    // Invalid regex pattern
    $condition = [
        'actions' => [],
        'conditions' => [
            'operatorIdentifier' => 'and',
            'children' => [
                [
                    'identifier' => $targetField['id'],
                    'value' => [
                        'operator' => 'matches_regex',
                        'property_meta' => [
                            'id' => $targetField['id'],
                            'type' => 'text',
                        ],
                        'value' => '[Invalid Regex)', // Invalid regex pattern
                    ],
                ],
            ],
        ],
    ];

    $submissionData = [];
    $validationMessage = 'Invalid regex pattern';

    $form->properties = collect($form->properties)->map(function ($property) use (&$submissionData, &$condition, &$validationMessage, $targetField) {
        if (in_array($property['name'], ['Name'])) {
            $property['validation'] = ['error_conditions' => $condition, 'error_message' => $validationMessage];
            $submissionData[$targetField['id']] = 'test@gmail.com';
        }
        return $property;
    })->toArray();

    $form->update();
    $formData = FormSubmissionDataFactory::generateSubmissionData($form, $submissionData);

    $this->postJson(route('forms.answer', $form->slug), $formData)
        ->assertStatus(422)
        ->assertJson([
            'message' => $validationMessage,
        ]);
});
