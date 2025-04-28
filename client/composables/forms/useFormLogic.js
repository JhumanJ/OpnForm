import FormPropertyLogicRule from '~/lib/forms/FormPropertyLogicRule.js'

export const useFormLogic = () => {
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
      
      // Apply comprehensive validation from FormPropertyLogicRule
      const logicRule = new FormPropertyLogicRule(field)
      if (!logicRule.isValid()) {
        errors.push({
          fieldId: field.id,
          fieldName: field.name,
          error: 'INVALID_LOGIC_RULE',
          message: logicRule.isConditionCorrect ? 
            'Invalid action configuration' : 
            'Invalid condition configuration'
        })
      }
    })

    return errors
  }

  const cleanInvalidLogic = (properties) => {
    // Create a deep copy to avoid mutating the original
    const cleanedProperties = JSON.parse(JSON.stringify(properties))

    // Get all validation errors
    const errors = getLogicErrors(cleanedProperties)
    
    // Group errors by fieldId for efficient processing
    const errorsByField = errors.reduce((acc, error) => {
      if (!acc[error.fieldId]) {
        acc[error.fieldId] = []
      }
      acc[error.fieldId].push(error)
      return acc
    }, {})

    // Clean invalid logic for each field
    cleanedProperties.forEach((field) => {
      const fieldErrors = errorsByField[field.id]
      
      // If field has any errors, reset its logic
      if (fieldErrors?.length > 0) {
        field.logic = {
          conditions: null,
          actions: [],
        }
      }
    })

    return cleanedProperties
  }
  
  // Direct implementation of the previous validatePropertiesLogic function
  const validatePropertiesLogic = (properties) => {
    // Create a deep copy to avoid mutating the original
    const validatedProperties = JSON.parse(JSON.stringify(properties))
    
    validatedProperties.forEach((field) => {
      const isValid = new FormPropertyLogicRule(field).isValid()
      if (!isValid) {
        field.logic = {
          conditions: null,
          actions: [],
        }
      }
    })
    
    return validatedProperties
  }

  return {
    validateCondition,
    getLogicErrors,
    cleanInvalidLogic,
    validatePropertiesLogic
  }
} 