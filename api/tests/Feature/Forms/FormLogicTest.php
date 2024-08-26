<?php

use Illuminate\Testing\Fluent\AssertableJson;

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
