import { conditionsMet } from './FormLogicConditionChecker'
class FormLogicPropertyResolver {
  conditionsMet = conditionsMet;
  property = null;
  formData = null;
  logic = false;

  constructor (property, formData) {
    this.property = property
    this.formData = formData
    this.logic = (property.logic !== undefined) ? property.logic : false
  }

  isHidden () {
    if (!this.logic) {
      return this.property.hidden
    }

    const conditionsMet = this.conditionsMet(this.logic.conditions, this.formData)
    if (conditionsMet && this.property.hidden && this.logic.actions.length > 0 && this.logic.actions.includes('show-block')) {
      return false
    } else if (conditionsMet && !this.property.hidden && this.logic.actions.length > 0 && this.logic.actions.includes('hide-block')) {
      return true
    } else {
      return this.property.hidden
    }
  }

  isRequired () {
    if (!this.logic) {
      return this.property.required
    }

    const conditionsMet = this.conditionsMet(this.logic.conditions, this.formData)
    if (conditionsMet && this.property.required && this.logic.actions.length > 0 && this.logic.actions.includes('make-it-optional')) {
      return false
    } else if (conditionsMet && !this.property.required && this.logic.actions.length > 0 && this.logic.actions.includes('require-answer')) {
      return true
    } else {
      return this.property.required
    }
  }
}

export default FormLogicPropertyResolver
