export const useLogicValidation = () => {
  const validateCondition = (condition, properties, fieldId, errors, parentIndex = null) => {
    if (condition.operatorIdentifier) {
      // This is a group condition (and/or)
      if (!Array.isArray(condition.children)) {
        errors.push({
          fieldId,
          error: 'INVALID_CONDITION_GROUP',
          message: 'Condition group must have children array'
        })
        return
      }
      
      condition.children.forEach((childCondition, index) => {
        validateCondition(childCondition, properties, fieldId, errors, parentIndex || index)
      })
    } else {
      // This is a leaf condition
      const referencedField = properties.find(p => p.id === condition.value?.property_meta?.id)
      if (!referencedField) {
        errors.push({
          fieldId,
          ruleIndex: parentIndex,
          error: 'INVALID_FIELD_REFERENCE',
          referencedFieldId: condition.value?.property_meta?.id,
          message: `Referenced field ${condition.value?.property_meta?.id} no longer exists`
        })
      }
    }
  }

  const getLogicErrors = (properties) => {
    const errors = []

    properties.forEach((field) => {
      // Skip if field has no logic configured at all
      if (!field.logic) return
      
      // Skip if field has default empty logic (null conditions and empty actions)
      if (field.logic.conditions === null && (!field.logic.actions || field.logic.actions.length === 0)) return

      // Now validate if logic is configured but missing required parts
      if (!field.logic.conditions) {
        errors.push({
          fieldId: field.id,
          fieldName: field.name,
          error: 'MISSING_CONDITIONS',
          message: 'No conditions specified'
        })
      }

      if (!field.logic.actions || field.logic.actions.length === 0) {
        errors.push({
          fieldId: field.id,
          fieldName: field.name,
          error: 'MISSING_ACTIONS',
          message: 'No actions specified'
        })
      }

      // Validate conditions structure and field references
      if (field.logic.conditions) {
        validateCondition(field.logic.conditions, properties, field.id, errors)
      }
    })

    return errors
  }

  return {
    getLogicErrors
  }
} 