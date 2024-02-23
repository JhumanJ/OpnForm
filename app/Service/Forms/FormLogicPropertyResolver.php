<?php

namespace App\Service\Forms;

class FormLogicPropertyResolver
{
    private $property = [];

    private $formData = [];

    private $logic = false;

    public function __construct(private array $prop, private array $values)
    {
        $this->property = $prop;
        $this->formData = $values;
        $this->logic = isset($this->property['logic']) ? $this->property['logic'] : false;
    }

    public static function isRequired(array $property, array $values): bool
    {
        return (new self($property, $values))->shouldBeRequired();
    }

    public static function isHidden(array $property, array $values): bool
    {
        return (new self($property, $values))->shouldBeHidden();
    }

    public function shouldBeRequired(): bool
    {
        if (! isset($this->property['required'])) {
            return false;
        }

        if (! $this->logic) {
            return $this->property['required'];
        }

        $conditionsMet = FormLogicConditionChecker::conditionsMet($this->logic['conditions'], $this->formData);
        if ($conditionsMet && $this->property['required'] && count($this->logic['actions']) > 0 && (in_array('make-it-optional', $this->logic['actions']) || in_array('hide-block', $this->logic['actions']))) {
            return false;
        } elseif ($conditionsMet && ! $this->property['required'] && count($this->logic['actions']) > 0 && in_array('require-answer', $this->logic['actions'])) {
            return true;
        } else {
            return $this->property['required'];
        }
    }

    public function shouldBeHidden(): bool
    {
        if (! isset($this->property['hidden'])) {
            return false;
        }

        if (! $this->logic) {
            return $this->property['hidden'];
        }

        $conditionsMet = FormLogicConditionChecker::conditionsMet($this->logic['conditions'], $this->formData);
        if ($conditionsMet && $this->property['hidden'] && count($this->logic['actions']) > 0 && in_array('show-block', $this->logic['actions'])) {
            return false;
        } elseif ($conditionsMet && ! $this->property['hidden'] && count($this->logic['actions']) > 0 && in_array('hide-block', $this->logic['actions'])) {
            return true;
        } else {
            return $this->property['hidden'];
        }
    }
}
