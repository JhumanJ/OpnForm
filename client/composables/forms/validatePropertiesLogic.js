import FormPropertyLogicRule from "~/lib/forms/FormPropertyLogicRule.js"

export const validatePropertiesLogic = (properties) => {
  properties.forEach((field) => {
    const isValid = new FormPropertyLogicRule(field).isValid()
    console.log('field', field)
    console.log('isValid', isValid, field.name)
    if (!isValid) {
      field.logic = {
        conditions: null,
        actions: [],
      }
    }
  })
  return properties
}
