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

it('can validate form logic rules for operators without values', function () {
    $rules = [
        'properties.*.logic' => ['array', 'nullable', new FormPropertyLogicRule()],
    ];

    // Test checkbox is_checked without value
    $data = [
        'properties' => [
            [
                'id' => 'checkbox1',
                'name' => 'Checkbox Field',
                'type' => 'checkbox',
                'logic' => [
                    'conditions' => [
                        'operatorIdentifier' => 'and',
                        'children' => [
                            [
                                'identifier' => 'test-id',
                                'value' => [
                                    'operator' => 'is_checked',
                                    'property_meta' => [
                                        'id' => 'test-id',
                                        'type' => 'checkbox'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'actions' => ['show-block']
                ]
            ]
        ]
    ];
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertTrue($validatorObj->passes());

    // Test checkbox is_checked with value (should still pass for backward compatibility)
    $data['properties'][0]['logic']['conditions']['children'][0]['value']['value'] = true;
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertTrue($validatorObj->passes());

    // Test checkbox is_not_checked without value
    $data['properties'][0]['logic']['conditions']['children'][0]['value']['operator'] = 'is_not_checked';
    unset($data['properties'][0]['logic']['conditions']['children'][0]['value']['value']);
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertTrue($validatorObj->passes());

    // Test checkbox with operator that doesn't exist
    $data['properties'][0]['logic']['conditions']['children'][0]['value']['operator'] = 'invalid_operator';
    $validatorObj = $this->app['validator']->make($data, $rules);
    $this->assertFalse($validatorObj->passes());
    expect($validatorObj->errors()->messages()['properties.0.logic'][0])->toBe('The logic conditions for Checkbox Field are not complete. Error detail(s): configuration not found for condition operator');
});
