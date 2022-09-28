export function conditionsMet (conditions, formData) {
  if (conditions === undefined || conditions === null) {
    return false
  }

  // If it's not a group, just a single condition
  if (conditions.operatorIdentifier === undefined) {
    return propertyConditionMet(conditions.value, conditions.value ? formData[conditions.value.property_meta.id] : null)
  }

  if (conditions.operatorIdentifier === 'and') {
    let isvalid = true
    conditions.children.forEach(childrenCondition => {
      if (!conditionsMet(childrenCondition, formData)) {
        isvalid = false
      }
    })
    return isvalid
  } else if (conditions.operatorIdentifier === 'or') {
    let isvalid = false
    conditions.children.forEach(childrenCondition => {
      if (conditionsMet(childrenCondition, formData)) {
        isvalid = true
      }
    })
    return isvalid
  }

  throw new Error('Unexcepted operatorIdentifier:' + conditions.operatorIdentifier)
}

function propertyConditionMet (propertyCondition, value) {
  if (!propertyCondition) {
    return false
  }
  switch (propertyCondition.property_meta.type) {
    case 'text':
    case 'url':
    case 'email':
    case 'phone_number':
      return textConditionMet(propertyCondition, value)
    case 'number':
      return numberConditionMet(propertyCondition, value)
    case 'checkbox':
      return checkboxConditionMet(propertyCondition, value)
    case 'select':
      return selectConditionMet(propertyCondition, value)
    case 'date':
      return dateConditionMet(propertyCondition, value)
    case 'multi_select':
      return multiSelectConditionMet(propertyCondition, value)
    case 'files':
      return filesConditionMet(propertyCondition, value)
  }
  return false
}

function checkEquals (condition, fieldValue) {
  return condition.value === fieldValue
}

function checkContains (condition, fieldValue) {
  return (fieldValue) ? fieldValue.includes(condition.value) : false
}

function checkListContains (condition, fieldValue) {
  if (!fieldValue) return false
  
  if (Array.isArray(condition.value)) {
    return condition.value.every(r => fieldValue.includes(r))
  } else {
    return fieldValue.includes(condition.value)
  }
}

function checkStartsWith (condition, fieldValue) {
  return fieldValue.startsWith(condition.value)
}

function checkendsWith (condition, fieldValue) {
  return fieldValue && fieldValue.endsWith(condition.value)
}

function checkIsEmpty (condition, fieldValue) {
  return (!fieldValue || fieldValue.length === 0)
}

function checkGreaterThan (condition, fieldValue) {
  return (condition.value && fieldValue && parseFloat(fieldValue) > parseFloat(condition.value))
}

function checkGreaterThanEqual (condition, fieldValue) {
  return (condition.value && fieldValue && parseFloat(fieldValue) >= parseFloat(condition.value))
}

function checkLessThan (condition, fieldValue) {
  return (condition.value && fieldValue && parseFloat(fieldValue) < parseFloat(condition.value))
}

function checkLessThanEqual (condition, fieldValue) {
  return (condition.value && fieldValue && parseFloat(fieldValue) <= parseFloat(condition.value))
}

function checkBefore (condition, fieldValue) {
  return (condition.value && fieldValue && fieldValue > condition.value)
}

function checkAfter (condition, fieldValue) {
  return (condition.value && fieldValue && fieldValue < condition.value)
}

function checkOnOrBefore (condition, fieldValue) {
  return (condition.value && fieldValue && fieldValue >= condition.value)
}

function checkOnOrAfter (condition, fieldValue) {
  return (condition.value && fieldValue && fieldValue <= condition.value)
}

function checkPastWeek (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate <= today && fieldDate >= new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7))
}

function checkPastMonth (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate <= today && fieldDate >= new Date(today.getFullYear(), today.getMonth() - 1, today.getDate()))
}

function checkPastYear (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate <= today && fieldDate >= new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()))
}

function checkNextWeek (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate >= today && fieldDate <= new Date(today.getFullYear(), today.getMonth(), today.getDate() + 7))
}

function checkNextMonth (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate >= today && fieldDate <= new Date(today.getFullYear(), today.getMonth() + 1, today.getDate()))
}

function checkNextYear (condition, fieldValue) {
  if (!fieldValue) return false
  const fieldDate = new Date(fieldValue)
  const today = new Date()
  return (fieldDate >= today && fieldDate <= new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()))
}

function textConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'equals':
      return checkEquals(propertyCondition, value)
    case 'does_not_equal':
      return !checkEquals(propertyCondition, value)
    case 'contains':
      return checkContains(propertyCondition, value)
    case 'does_not_contain':
      return !checkContains(propertyCondition, value)
    case 'starts_with':
      return checkStartsWith(propertyCondition, value)
    case 'ends_with':
      return checkendsWith(propertyCondition, value)
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'is_not_empty':
      return !checkIsEmpty(propertyCondition, value)
  }
  return false
}

function numberConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'equals':
      return checkEquals(propertyCondition, value)
    case 'does_not_equal':
      return !checkEquals(propertyCondition, value)
    case 'greater_than':
      return checkGreaterThan(propertyCondition, value)
    case 'less_than':
      return checkLessThan(propertyCondition, value)
    case 'greater_than_or_equal_to':
      return checkGreaterThanEqual(propertyCondition, value)
    case 'less_than_or_equal_to':
      return checkLessThanEqual(propertyCondition, value)
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'is_not_empty':
      return !checkIsEmpty(propertyCondition, value)
  }
  return false
}

function checkboxConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'equals':
      return checkEquals(propertyCondition, value)
    case 'does_not_equal':
      return !checkEquals(propertyCondition, value)
  }
  return false
}

function selectConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'equals':
      return checkEquals(propertyCondition, value)
    case 'does_not_equal':
      return !checkEquals(propertyCondition, value)
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'is_not_empty':
      return !checkIsEmpty(propertyCondition, value)
  }
  return false
}

function dateConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'equals':
      return checkEquals(propertyCondition, value)
    case 'before':
      return checkBefore(propertyCondition, value)
    case 'after':
      return checkAfter(propertyCondition, value)
    case 'on_or_before':
      return checkOnOrBefore(propertyCondition, value)
    case 'on_or_after':
      return checkOnOrAfter(propertyCondition, value)
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'past_week':
      return checkPastWeek(propertyCondition, value)
    case 'past_month':
      return checkPastMonth(propertyCondition, value)
    case 'past_year':
      return checkPastYear(propertyCondition, value)
    case 'next_week':
      return checkNextWeek(propertyCondition, value)
    case 'next_month':
      return checkNextMonth(propertyCondition, value)
    case 'next_year':
      return checkNextYear(propertyCondition, value)
  }
  return false
}

function multiSelectConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'contains':
      return checkListContains(propertyCondition, value)
    case 'does_not_contain':
      return !checkListContains(propertyCondition, value)
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'is_not_empty':
      return !checkIsEmpty(propertyCondition, value)
  }
  return false
}

function filesConditionMet (propertyCondition, value) {
  switch (propertyCondition.operator) {
    case 'is_empty':
      return checkIsEmpty(propertyCondition, value)
    case 'is_not_empty':
      return !checkIsEmpty(propertyCondition, value)
  }
  return false
}
