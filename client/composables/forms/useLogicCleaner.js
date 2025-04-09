import { useLogicValidation } from './useLogicValidation'

export const useLogicCleaner = () => {
  const { getLogicErrors } = useLogicValidation()

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

  return {
    cleanInvalidLogic
  }
} 