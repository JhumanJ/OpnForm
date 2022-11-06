<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Str;

class FormPropertyLogicRule implements Rule, DataAwareRule 
{

    const ACTIONS_VALUES = [
        'show-block',
        'hide-block',
        'make-it-optional',
        'require-answer'
    ];

    const CONDITION_MAPPING = [
        'text' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                ],
                'does_not_equal' => [
                    'expected_type' => 'string',
                ],
                'contains' => [
                    'expected_type' => 'string',
                ],
                'does_not_contain' => [
                    'expected_type' => 'string',
                ],
                'starts_with' => [
                    'expected_type' => 'string',
                ],
                'ends_with' => [
                    'expected_type' => 'string',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'url' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                ],
                'does_not_equal' => [
                    'expected_type' => 'string',
                ],
                'contains' => [
                    'expected_type' => 'string',
                ],
                'does_not_contain' => [
                    'expected_type' => 'string',
                ],
                'starts_with' => [
                    'expected_type' => 'string',
                ],
                'ends_with' => [
                    'expected_type' => 'string',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'email' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                ],
                'does_not_equal' => [
                    'expected_type' => 'string',
                ],
                'contains' => [
                    'expected_type' => 'string',
                ],
                'does_not_contain' => [
                    'expected_type' => 'string',
                ],
                'starts_with' => [
                    'expected_type' => 'string',
                ],
                'ends_with' => [
                    'expected_type' => 'string',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'phone_number' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                ],
                'does_not_equal' => [
                    'expected_type' => 'string',
                ],
                'contains' => [
                    'expected_type' => 'string',
                ],
                'does_not_contain' => [
                    'expected_type' => 'string',
                ],
                'starts_with' => [
                    'expected_type' => 'string',
                ],
                'ends_with' => [
                    'expected_type' => 'string',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'number' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'number',
                ],
                'does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'greater_than' => [
                    'expected_type' => 'number',
                ],
                'less_than' => [
                    'expected_type' => 'number',
                ],
                'greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'checkbox' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'does_not_equal' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],

            ]
        ],
        'select' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                ],
                'does_not_equal' => [
                    'expected_type' => 'string',
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'multi_select' => [
            'comparators' => [
                'contains' => [
                    'expected_type' => ['object', 'string'],
                    'format' => [
                        'type' => 'uuid',
                    ]
                ],
                'does_not_contain' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'uuid',
                    ]
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
        'date' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date'
                    ]
                ],
                'before' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date'
                    ]
                ],
                'after' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date'
                    ]
                ],
                'on_or_before' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date'
                    ]
                ],
                'on_or_after' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date'
                    ]
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'past_week' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ],
                'past_month' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ],
                'past_year' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ],
                'next_week' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ],
                'next_month' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ],
                'next_year' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}'
                    ]
                ]
            ]
        ],
        'files' => [
            'comparators' => [
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true]
                    ]
                ]
            ]
        ],
    ];

    private $isConditionCorrect = true;
    private $isActionCorrect = true;
    private $field = [];
    private $data = [];

    private function checkBaseCondition($condition) 
    {

        if (!isset($condition['value'])) {
            $this->isConditionCorrect = false;
            return;
        }

        if (!isset($condition['value']['property_meta'])) {
            $this->isConditionCorrect = false;
            return;
        }

        if (!isset($condition['value']['property_meta']['type'])) {
            $this->isConditionCorrect = false;
            return;
        }
        
        if (!isset($condition['value']['operator'])) {
            $this->isConditionCorrect = false;
            return;
        }

        if (!isset($condition['value']['value'])) {
            $this->isConditionCorrect = false;
            return;
        }

        $typeField = $condition['value']['property_meta']['type'];
        $operator = $condition['value']['operator'];
        $value = $condition['value']['value'];

        if (!isset(self::CONDITION_MAPPING[$typeField])) {
            $this->isConditionCorrect = false;
            return;
        }

        if (!isset(self::CONDITION_MAPPING[$typeField]['comparators'][$operator])) {
            $this->isConditionCorrect = false;
            return;
        }

        $type = self::CONDITION_MAPPING[$typeField]['comparators'][$operator]['expected_type'];

        if (is_array($type)) {
            $foundCorrectType = false;
            foreach ($type as $subtype) {
                if ($this->valueHasCorrectType($subtype, $value)) {
                    $foundCorrectType = true;
                }
            }
            if (!$foundCorrectType) {
                $this->isConditionCorrect = false;
            }
        } else {
            if (!$this->valueHasCorrectType($type, $value)) {
                $this->isConditionCorrect = false;
            }
        }
    }

    private function valueHasCorrectType($type, $value)
    {
        if (
            ($type === 'string' && gettype($value) !== 'string') ||
            ($type === 'boolean' && !is_bool($value)) ||
            ($type === 'number' && !is_numeric($value)) ||
            ($type === 'object' && !is_array($value))
        ) {
            return false;
        }
        return true;
    }

    private function checkConditions($conditions) 
    {
        if (isset($conditions['operatorIdentifier'])) {
            if (($conditions['operatorIdentifier'] !== 'and') && ($conditions['operatorIdentifier'] !== 'or')) {
                $this->isConditionCorrect = false;
                return;
            }

            if (isset($conditions['operatorIdentifier']['children'])) {
                $this->isConditionCorrect = false;
                return;
            }

            if (!is_array($conditions['children'])) {
                $this->isConditionCorrect = false;
                return;
            }

            foreach ($conditions['children'] as &$child) {
                $this->checkConditions($child);
            }
        } else if (isset($conditions['identifier'])) {
            $this->checkBaseCondition($conditions);
        }
    }

    private function checkActions($conditions) 
    {
        if (is_array($conditions) && count($conditions) > 0) {
            foreach($conditions as $val){
                if (!in_array($val, static::ACTIONS_VALUES) || 
                    (in_array($this->field["type"], ['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image']) && !in_array($val, ['hide-block'])) ||
                    (isset($this->field["hidden"]) && $this->field["hidden"] && !in_array($val, ['show-block', 'require-answer'])) || 
                    (isset($this->field["required"]) && $this->field["required"] && !in_array($val, ['make-it-optional', 'hide-block']))
                ) {
                    $this->isActionCorrect = false;
                    break;
                }
            }
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) 
    {
        $this->setProperty($attribute);
        if(isset($value["conditions"])){
            $this->checkConditions($value["conditions"]);
        }
        if(isset($value["actions"])){
            $this->checkActions($value["actions"]);
        }
        return ($this->isConditionCorrect && $this->isActionCorrect);
    }

    /**
     * Get the validation error message.
     *
     */
    public function message() 
    {
        $errorList = [];
        if(!$this->isConditionCorrect){
            $errorList[] = "The logic conditions for ".$this->field['name']." are not complete.";
        }
        if(!$this->isActionCorrect){
            $errorList[] = "The logic actions for ".$this->field['name']." are not valid.";
        }
        return $errorList;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    private function setProperty(string $attributeKey)
    {
        $attributeKey = Str::of($attributeKey)->replace('.logic', '')->toString();
        $this->field = \Arr::get($this->data, $attributeKey);
    }
}
