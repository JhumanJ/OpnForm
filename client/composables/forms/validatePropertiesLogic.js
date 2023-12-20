import FormPropertyLogicRule from "~/lib/forms/FormPropertyLogicRule.js";

export const validatePropertiesLogic = (properties) => {
  properties.forEach((field) => {
    const isValid = (new FormPropertyLogicRule(field)).isValid()
    if (!isValid) {
      field.logic = {
        conditions: null,
        actions: []
      }
    }
  })
  return properties
}
