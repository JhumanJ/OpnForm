<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class FormPropertyLogicRule implements DataAwareRule, Rule
{
    public const ACTIONS_VALUES = [
        'show-block',
        'hide-block',
        'make-it-optional',
        'require-answer',
        'enable-block',
        'disable-block',
    ];

    public const CONDITION_MAPPING = [
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
        ],
        'rating' => [
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
        ],
        'scale' => [
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
        ],
        'slider' => [
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'content_length_equals' => [
                    'expected_type' => 'number',
                ],
                'content_length_does_not_equal' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_greater_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than' => [
                    'expected_type' => 'number',
                ],
                'content_length_less_than_or_equal_to' => [
                    'expected_type' => 'number',
                ],
            ],
        ],
        'checkbox' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'does_not_equal' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],

            ],
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
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
            ],
        ],
        'multi_select' => [
            'comparators' => [
                'contains' => [
                    'expected_type' => ['object', 'string'],
                    'format' => [
                        'type' => 'uuid',
                    ],
                ],
                'does_not_contain' => [
                    'expected_type' => ['object', 'string'],
                    'format' => [
                        'type' => 'uuid',
                    ],
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
            ],
        ],
        'date' => [
            'comparators' => [
                'equals' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date',
                    ],
                ],
                'before' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date',
                    ],
                ],
                'after' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date',
                    ],
                ],
                'on_or_before' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date',
                    ],
                ],
                'on_or_after' => [
                    'expected_type' => 'string',
                    'format' => [
                        'type' => 'date',
                    ],
                ],
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'past_week' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
                'past_month' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
                'past_year' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
                'next_week' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
                'next_month' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
                'next_year' => [
                    'expected_type' => 'object',
                    'format' => [
                        'type' => 'empty',
                        'values' => '{}',
                    ],
                ],
            ],
        ],
        'files' => [
            'comparators' => [
                'is_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
                'is_not_empty' => [
                    'expected_type' => 'boolean',
                    'format' => [
                        'type' => 'enum',
                        'values' => [true],
                    ],
                ],
            ],
        ],
    ];

    private $isConditionCorrect = true;

    private $isActionCorrect = true;

    private $conditionErrors = [];

    private $field = [];

    private $data = [];

    private function checkBaseCondition($condition)
    {

        if (!isset($condition['value'])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'missing condition body';

            return;
        }

        if (!isset($condition['value']['property_meta'])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'missing condition property';

            return;
        }

        if (!isset($condition['value']['property_meta']['type'])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'missing condition property type';

            return;
        }

        if (!isset($condition['value']['operator'])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'missing condition operator';

            return;
        }

        if (!isset($condition['value']['value'])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'missing condition value';

            return;
        }

        $typeField = $condition['value']['property_meta']['type'];
        $operator = $condition['value']['operator'];
        $value = $condition['value']['value'];

        if (!isset(self::CONDITION_MAPPING[$typeField])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'configuration not found for condition type';

            return;
        }

        if (!isset(self::CONDITION_MAPPING[$typeField]['comparators'][$operator])) {
            $this->isConditionCorrect = false;
            $this->conditionErrors[] = 'configuration not found for condition operator';

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
                $this->conditionErrors[] = 'wrong type of condition value';
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
        if (array_key_exists('operatorIdentifier', $conditions)) {
            if (($conditions['operatorIdentifier'] !== 'and') && ($conditions['operatorIdentifier'] !== 'or')) {
                $this->conditionErrors[] = 'missing operator';
                $this->isConditionCorrect = false;

                return;
            }

            if (isset($conditions['operatorIdentifier']['children'])) {
                $this->conditionErrors[] = 'extra condition';
                $this->isConditionCorrect = false;

                return;
            }

            if (!is_array($conditions['children'])) {
                $this->conditionErrors[] = 'wrong sub-condition type';
                $this->isConditionCorrect = false;

                return;
            }

            foreach ($conditions['children'] as &$child) {
                $this->checkConditions($child);
            }
        } elseif (isset($conditions['identifier'])) {
            $this->checkBaseCondition($conditions);
        }
    }

    private function checkActions($actions)
    {
        if (is_array($actions) && count($actions) > 0) {
            foreach ($actions as $val) {
                if (
                    !in_array($val, static::ACTIONS_VALUES) ||
                    (in_array($this->field['type'], ['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image']) && !in_array($val, ['hide-block', 'show-block'])) ||
                    (isset($this->field['hidden']) && $this->field['hidden'] && !in_array($val, ['show-block', 'require-answer'])) ||
                    (isset($this->field['required']) && $this->field['required'] && !in_array($val, ['make-it-optional', 'hide-block', 'disable-block'])) ||
                    (isset($this->field['disabled']) && $this->field['disabled'] && !in_array($val, ['enable-block', 'require-answer', 'make-it-optional']))
                ) {
                    $this->isActionCorrect = false;
                    break;
                }
            }
        } else {
            $this->isActionCorrect = false;
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
        if (isset($value['conditions'])) {
            $this->checkConditions($value['conditions']);
            $this->checkActions($value['actions'] ?? null);
        }

        return $this->isConditionCorrect && $this->isActionCorrect;
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        $message = null;
        if (!$this->isConditionCorrect) {
            $message = 'The logic conditions for ' . $this->field['name'] . ' are not complete.';
        } elseif (!$this->isActionCorrect) {
            $message = 'The logic actions for ' . $this->field['name'] . ' are not valid.';
        }
        if (count($this->conditionErrors) > 0) {
            return $message . ' Error detail(s): ' . implode(', ', $this->conditionErrors);
        }

        return $message;
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
        $this->isConditionCorrect = true;
        $this->isActionCorrect = true;
        $this->conditionErrors = [];

        return $this;
    }

    private function setProperty(string $attributeKey)
    {
        $attributeKey = Str::of($attributeKey)->replace('.logic', '')->toString();
        $this->field = \Arr::get($this->data, $attributeKey);
    }
}
