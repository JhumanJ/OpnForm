export class FormValidationService {
  constructor(form, formData) {
    this.form = form
    this.formData = formData
  }

  /**
   * Validate specific fields
   */
  async validateFields(fieldsToValidate) {
    if (!fieldsToValidate || fieldsToValidate.length === 0) {
      return true
    }

    try {
      this.formData.busy = true
      await this.formData.validate('POST', `/forms/${this.form.slug}/answer`, {}, fieldsToValidate)
      return true
    } catch (error) {
      if (error?.data?.errors) {
        useAlert().formValidationError(error.data)
      }
      return false
    } finally {
      this.formData.busy = false
    }
  }

  /**
   * Validate current page fields
   */
  async validateCurrentPage(currentFields, formModeStrategy) {
    if (!formModeStrategy.validation.validateOnNextPage) {
      return true
    }

    const fieldsToValidate = currentFields
      .filter(f => f.type !== 'payment')
      .map(f => f.id)

    return await this.validateFields(fieldsToValidate)
  }

  /**
   * Handle validation error
   */
  handleValidationError(error) {
    console.error(error)
    if (error?.data) {
      useAlert().formValidationError(error.data)
    }
    this.formData.busy = false
  }

  /**
   * Show first page with validation error
   */
  showFirstPageWithError(fieldGroups, setPageIndex) {
    for (let i = 0; i < fieldGroups.length; i++) {
      if (fieldGroups[i].some(field => this.formData.errors.has(field.id))) {
        setPageIndex(i)
        break
      }
    }
  }

  /**
   * Scroll to first error element
   */
  scrollToFirstError() {
    if (import.meta.server) return
    const firstErrorElement = document.querySelector('.has-error')
    if (firstErrorElement) {
      window.scroll({
        top: window.scrollY + firstErrorElement.getBoundingClientRect().top - 60,
        behavior: 'smooth'
      })
    }
  }

  /**
   * Handle form submission failure
   */
  onSubmissionFailure(fieldGroups, setPageIndex, formTimer) {
    formTimer?.startTimer()
    this.formData.busy = false
    
    if (fieldGroups.length > 1) {
      this.showFirstPageWithError(fieldGroups, setPageIndex)
    }
    this.scrollToFirstError()
  }

  /**
   * Validate form properties logic
   */
  validatePropertiesLogic(properties) {
    properties.forEach((field) => {
      const isValid = new FormPropertyLogicRule(field).isValid()
      if (!isValid) {
        field.logic = {
          conditions: null,
          actions: [],
        }
      }
    })
    return properties
  }
}
