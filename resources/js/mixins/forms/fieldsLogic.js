import FormPropertyLogicRule from '../../forms/FormPropertyLogicRule.js'
export default {
  methods: {
    validateFieldsLogic (properties) {
      properties.forEach((field) => {
        const isValid = (new FormPropertyLogicRule(field)).isValid()
        if(!isValid){
          field.logic = {
            conditions: null,
            actions: []
          }
        }
      })
      return properties
    }
  }
}
