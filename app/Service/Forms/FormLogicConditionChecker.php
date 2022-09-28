<?php

namespace App\Service\Forms;

use Mockery\Matcher\Any;

use function PHPUnit\Framework\isEmpty;

class FormLogicConditionChecker
{
    public function __construct(private ?array $conditions, private ?array $formData)
    {
    }

    public static function conditionsMet(?array $conditions, array $formData): bool {
        return (new self($conditions, $formData))->conditionsAreMet($conditions, $formData);
    }

    private function conditionsAreMet(?array $conditions, array $formData): bool {
        if (!$conditions) {
            return false;
        }

        // If it's not a group, just a single condition
        if (!isset($conditions['operatorIdentifier'])) {
            return $this->propertyConditionMet($conditions['value'], $formData[$conditions['value']['property_meta']['id']]);
        }

        if ($conditions['operatorIdentifier'] === 'and') {
            $isvalid = true;
            foreach($conditions['children'] as $childrenCondition){
                if (!$this->conditionsMet($childrenCondition, $formData)) {
                    $isvalid = false;
                    break;
                }
            }
            return $isvalid;
        } else if ($conditions['operatorIdentifier'] === 'or') {
            $isvalid = false;
            foreach($conditions['children'] as $childrenCondition){
                if ($this->conditionsMet($childrenCondition, $formData)) {
                    $isvalid = true;
                    break;
                }
            }
            return $isvalid;
        }

        throw new \Exception('Unexcepted operatorIdentifier:'. $conditions['operatorIdentifier']);
    }

    private function propertyConditionMet(array $propertyCondition, $value): bool {
        switch ($propertyCondition['property_meta']['type']) {
            case 'text':
            case 'url':
            case 'email':
            case 'phone_number':
              return $this->textConditionMet($propertyCondition, $value);
            case 'number':
              return $this->numberConditionMet($propertyCondition, $value);
            case 'checkbox':
              return $this->checkboxConditionMet($propertyCondition, $value);
            case 'select':
              return $this->selectConditionMet($propertyCondition, $value);
            case 'date':
              return $this->dateConditionMet($propertyCondition, $value);
            case 'multi_select':
              return $this->multiSelectConditionMet($propertyCondition, $value);
            case 'files':
              return $this->filesConditionMet($propertyCondition, $value);
          }
          return false;
    }

    private function checkEquals ($condition, $fieldValue): bool {
        return $condition['value'] === $fieldValue;
    }

    private function checkContains ($condition, $fieldValue): bool {
        return ($fieldValue && is_array($fieldValue)) ? in_array($condition['value'], $fieldValue) : false;
    }

    private function checkListContains ($condition, $fieldValue): bool {
        if (is_null($fieldValue)) return false;

        if (is_array($condition['value'])) {
            return count(array_intersect($condition['value'], $fieldValue)) === count($condition['value']);
        } else {
            return in_array($condition['value'], $fieldValue);
        }
    }

    private function checkStartsWith ($condition, $fieldValue): bool {
        return str_starts_with($fieldValue, $condition['value']);
    }

    private function checkendsWith ($condition, $fieldValue): bool {
        return str_ends_with($fieldValue, $condition['value']);
    }

    private function checkIsEmpty ($condition, $fieldValue): bool {
        if(is_array($fieldValue)){
            return count($fieldValue) === 0;
        }
        return (!$fieldValue || $fieldValue !== '' || $fieldValue !== null);
    }

    private function checkGreaterThan ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && (float)$fieldValue > (float)$condition['value']);
    }

    private function checkGreaterThanEqual ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && (float)$fieldValue >= (float)$condition['value']);
    }

    private function checkLessThan ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && (float)$fieldValue < (float)$condition['value']);
    }

    private function checkLessThanEqual ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && (float)$fieldValue <= (float)$condition['value']);
    }

    private function checkBefore ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && $fieldValue > $condition['value']);
    }

    private function checkAfter ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && $fieldValue < $condition['value']);
    }

    private function checkOnOrBefore ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && $fieldValue >= $condition['value']);
    }

    private function checkOnOrAfter ($condition, $fieldValue): bool {
        return ($condition['value'] && $fieldValue && $fieldValue <= $condition['value']);
    }

    private function checkPastWeek ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate <= now()->toDateString() && $fieldDate >= now()->subDays(7)->toDateString());
    }

    private function checkPastMonth ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate <= now()->toDateString() && $fieldDate >= now()->subMonths(1)->toDateString());
    }

    private function checkPastYear ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate <= now()->toDateString() && $fieldDate >= now()->subYears(1)->toDateString());
    }

    private function checkNextWeek ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate >= now()->toDateString() && $fieldDate <= now()->addDays(7)->toDateString());
    }

    private function checkNextMonth ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate >= now()->toDateString() && $fieldDate <= now()->addMonths(1)->toDateString());
    }

    private function checkNextYear ($condition, $fieldValue): bool {
        if(!$fieldValue) return false;
        $fieldDate = date('Y-m-d', strtotime($fieldValue));
        return ($fieldDate >= now()->toDateString() && $fieldDate <= now()->addYears(1)->toDateString());
    }


    private function textConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'equals':
            return $this->checkEquals($propertyCondition, $value);
          case 'does_not_equal':
            return !$this->checkEquals($propertyCondition, $value);
          case 'contains':
            return $this->checkContains($propertyCondition, $value);
          case 'does_not_contain':
            return !$this->checkContains($propertyCondition, $value);
          case 'starts_with':
            return $this->checkStartsWith($propertyCondition, $value);
          case 'ends_with':
            return $this->checkendsWith($propertyCondition, $value);
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'is_not_empty':
            return !$this->checkIsEmpty($propertyCondition, $value);
        }
        return false;
    }

    private function numberConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'equals':
            return $this->checkEquals($propertyCondition, $value);
          case 'does_not_equal':
            return !$this->checkEquals($propertyCondition, $value);
          case 'greater_than':
            return $this->checkGreaterThan($propertyCondition, $value);
          case 'less_than':
            return $this->checkLessThan($propertyCondition, $value);
          case 'greater_than_or_equal_to':
            return $this->checkGreaterThanEqual($propertyCondition, $value);
          case 'less_than_or_equal_to':
            return $this->checkLessThanEqual($propertyCondition, $value);
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'is_not_empty':
            return !$this->checkIsEmpty($propertyCondition, $value);
        }
        return false;
    }

    private function checkboxConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'equals':
            return $this->checkEquals($propertyCondition, $value);
          case 'does_not_equal':
            return !$this->checkEquals($propertyCondition, $value);
        }
        return false;
    }

    private function selectConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'equals':
            return $this->checkEquals($propertyCondition, $value);
          case 'does_not_equal':
            return !$this->checkEquals($propertyCondition, $value);
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'is_not_empty':
            return !$this->checkIsEmpty($propertyCondition, $value);
        }
        return false;
    }

    private function dateConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'equals':
            return $this->checkEquals($propertyCondition, $value);
          case 'before':
            return $this->checkBefore($propertyCondition, $value);
          case 'after':
            return $this->checkAfter($propertyCondition, $value);
          case 'on_or_before':
            return $this->checkOnOrBefore($propertyCondition, $value);
          case 'on_or_after':
            return $this->checkOnOrAfter($propertyCondition, $value);
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'past_week':
            return $this->checkPastWeek($propertyCondition, $value);
          case 'past_month':
            return $this->checkPastMonth($propertyCondition, $value);
          case 'past_year':
            return $this->checkPastYear($propertyCondition, $value);
          case 'next_week':
            return $this->checkNextWeek($propertyCondition, $value);
          case 'next_month':
            return $this->checkNextMonth($propertyCondition, $value);
          case 'next_year':
            return $this->checkNextYear($propertyCondition, $value);
        }
        return false;
    }

    private function multiSelectConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'contains':
            return $this->checkListContains($propertyCondition, $value);
          case 'does_not_contain':
            return !$this->checkListContains($propertyCondition, $value);
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'is_not_empty':
            return !$this->checkIsEmpty($propertyCondition, $value);
        }
        return false;
    }

    private function filesConditionMet (array $propertyCondition, $value): bool {
        switch ($propertyCondition['operator']) {
          case 'is_empty':
            return $this->checkIsEmpty($propertyCondition, $value);
          case 'is_not_empty':
            return !$this->checkIsEmpty($propertyCondition, $value);
        }
        return false;
    }
}
