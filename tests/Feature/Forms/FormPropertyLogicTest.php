<?php

use App\Rules\FormPropertyLogicRule;

it('can validate form logic rules for actions', function () {
    $rules = [
        'properties.*.logic' => ['array', 'nullable', new FormPropertyLogicRule()],
    ];

    $data = [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'title',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => null,
                    'actions' => [],
                ],
            ],
        ],
    ];
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertTrue($validatorObj->passes());

    $data = [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'title',
                'hidden' => true,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'title',
                                'value' => [
                                    'operator' => 'equals',
                                    'property_meta' => [
                                        'id' => 'title',
                                        'type' => 'text',
                                    ],
                                    'value' => 'TEST',
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['hide-block'],
                ],
            ],
        ],
    ];
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertFalse($validatorObj->passes());
    expect($validatorObj->errors()->messages()['properties.0.logic'][0])->toBe('The logic actions for Name are not valid.');

    $data = [
        'properties' => [
            [
                'id' => 'text',
                'name' => 'Custom Test',
                'type' => 'nf-text',
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'title',
                                'value' => [
                                    'operator' => 'equals',
                                    'property_meta' => [
                                        'id' => 'title',
                                        'type' => 'text',
                                    ],
                                    'value' => 'TEST',
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['require-answer'],
                ],
            ],
        ],
    ];
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertFalse($validatorObj->passes());
    expect($validatorObj->errors()->messages()['properties.0.logic'][0])->toBe('The logic actions for Custom Test are not valid.');
});

it('can validate form logic rules for conditions', function () {
    $rules = [
        'properties.*.logic' => ['array', 'nullable', new FormPropertyLogicRule()],
    ];

    $data = [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'title',
                                'value' => [
                                    'operator' => 'equals',
                                    'property_meta' => [
                                        'id' => 'title',
                                        'type' => 'text',
                                    ],
                                    'value' => 'TEST',
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['hide-block'],
                ],
            ],
        ],
    ];

    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertTrue($validatorObj->passes());

    $data = [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'title',
                                'value' => [
                                    'operator' => 'starts_with',
                                    'property_meta' => [
                                        'id' => 'title',
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['hide-block'],
                ],
            ],
        ],
    ];

    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertFalse($validatorObj->passes());
    expect($validatorObj->errors()->messages()['properties.0.logic'][0])->toBe('The logic conditions for Name are not complete. Error detail(s): missing condition value');

    $data = [
        'properties' => [
            [
                'id' => 'title',
                'name' => 'Name',
                'type' => 'text',
                'hidden' => false,
                'required' => false,
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => null,
                        'children' => [
                            [
                                'identifier' => 'title',
                                'value' => [
                                    'operator' => 'starts_with',
                                    'property_meta' => [
                                        'id' => 'title',
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'actions' => ['hide-block'],
                ],
            ],
        ],
    ];

    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertFalse($validatorObj->passes());
    expect($validatorObj->errors()->messages()['properties.0.logic'][0])->toBe('The logic conditions for Name are not complete. Error detail(s): missing operator');
});
