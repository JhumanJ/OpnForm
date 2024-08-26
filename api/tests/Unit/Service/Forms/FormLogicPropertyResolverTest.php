<?php

use App\Service\Forms\FormLogicPropertyResolver;

it('can validate form logic property resolver', function ($property, $formData, $expectedResult) {
    $isRequired = FormLogicPropertyResolver::isRequired($property, $formData);
    expect($isRequired)->toBe($expectedResult);
})->with([
    [
        [
            'id' => 'title',
            'name' => 'Name',
            'type' => 'text',
            'hidden' => false,
            'required' => true,
            'logic' => [
                'conditions' => [
                    'operatorIdentifier' => 'and',
                    'children' => [
                        [
                            'identifier' => 'user',
                            'value' => [
                                'operator' => 'is_not_empty',
                                'property_meta' => [
                                    'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee124',
                                    'type' => 'select',
                                ],
                                'value' => true,
                            ],
                        ],
                    ],
                ],
                'actions' => ['make-it-optional'],
            ],
        ],
        ['93ea3198-353f-440b-8dc9-2ac9a7bee124' => ['One']],
        false,
    ],
    [
        [
            'id' => 'title',
            'name' => 'Name',
            'type' => 'text',
            'hidden' => false,
            'required' => true,
            'logic' => [
                'conditions' => [
                    'operatorIdentifier' => 'and',
                    'children' => [
                        [
                            'identifier' => 'user',
                            'value' => [
                                'operator' => 'is_not_empty',
                                'property_meta' => [
                                    'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee124',
                                    'type' => 'select',
                                ],
                                'value' => true,
                            ],
                        ],
                    ],
                ],
                'actions' => ['make-it-optional'],
            ],
        ],
        ['93ea3198-353f-440b-8dc9-2ac9a7bee124' => []],
        true,
    ],
    [
        [
            'id' => 'title',
            'name' => 'Name',
            'type' => 'text',
            'hidden' => false,
            'required' => true,
            'logic' => [
                'conditions' => [
                    'operatorIdentifier' => 'or',
                    'children' => [
                        [
                            'identifier' => 'user',
                            'value' => [
                                'operator' => 'is_not_empty',
                                'property_meta' => [
                                    'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee124',
                                    'type' => 'select',
                                ],
                                'value' => true,
                            ],
                        ],
                        [
                            'identifier' => 'email',
                            'value' => [
                                'operator' => 'contains',
                                'property_meta' => [
                                    'id' => '93ea3198-353f-440b-8dc9-2ac9a7bee222',
                                    'type' => 'email',
                                ],
                                'value' => 'abc',
                            ],
                        ],
                    ],
                ],
                'actions' => ['make-it-optional'],
            ],
        ],
        ['93ea3198-353f-440b-8dc9-2ac9a7bee124' => [], '93ea3198-353f-440b-8dc9-2ac9a7bee222' => ['abc']],
        false,
    ],
]);
