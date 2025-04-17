import { pendingSubmission } from "~/composables/forms/pendingSubmission.js"

export class FormInitializationService {
    constructor(form, formData, options = {}) {
      this.form = form
      this.formData = formData
      this.isPublicFormPage = this.checkIfPublicFormPage()
      this.formPageIndex = options.formPageIndex
      this.isInitialLoad = options.isInitialLoad
    }
  
    async initialize(options = {}) {
      // Priority 1: Default data form
      if (options.defaultData) {
        return this.applyDefaultData(options.defaultData)
      }
  
      // Priority 2: Editable submission
      if (this.form.editable_submissions && options.submissionId) {
        const data = await this.fetchSubmissionData(options.submissionId)
        if (data) {
          return this.applySubmissionData(data)
        }
      }
  
      // Priority 3: Pending/auto-saved submission
      if (this.form.auto_save && this.isPublicFormPage) {
        const pendingData = this.getPendingSubmission()
        if (pendingData) {
          return this.applyPendingData(pendingData)
        }
      }
  
      // Priority 4: URL parameters and default values
      return this.applyDefaultValues(options.urlParams)
    }
  
    async fetchSubmissionData(submissionId) {
      try {
        const data = await opnFetch(`/forms/${this.form.slug}/submissions/${submissionId}`)
        return { submission_id: submissionId, id: submissionId, ...data.data }
      } catch (error) {
        if (error?.data?.errors) {
          useAlert().formValidationError(error.data)
        } else {
          useAlert().error(error?.data?.message || 'Something went wrong')
        }
        return null
      }
    }
  
    getPendingSubmission() {
      if (!this.form) return null
  
      const pendingSub = pendingSubmission(this.form)
      const data = pendingSub.get()
  
      if (data && Object.keys(data).length !== 0) {
        return data
      }
  
      return null
    }
  
    applyDefaultData(defaultData) {
      this.formData.resetAndFill(defaultData)
      return true
    }
  
    applySubmissionData(data) {
      this.formData.resetAndFill(data)
      return true
    }
  
    applyPendingData(pendingData) {
      // Update fields like date with prefill_today
      this.form.properties.forEach(field => {
        if (field.type === 'date' && field.prefill_today) {
          pendingData[field.id] = new Date().toISOString()
        }
      })
  
      this.formData.resetAndFill(pendingData)
      return true
    }
  
    applyDefaultValues(urlParams = null) {
      const formData = {}
  
      this.form.properties.forEach(field => {
        if (field.type.startsWith('nf-') &&
            !['nf-page-body-input', 'nf-page-logo', 'nf-page-cover'].includes(field.type)) {
          return
        }
  
        this.handleUrlPrefill(field, formData, urlParams)
        this.handleDefaultPrefill(field, formData)
      })
  
      // Only set page 0 on first load, otherwise maintain current position
      if (this.formPageIndex === undefined || this.isInitialLoad) {
        this.formPageIndex = 0
        this.isInitialLoad = false
      }

      this.formData.resetAndFill(formData)
      return true
    }
  
    handleUrlPrefill(field, formData, urlParams) {
      if (!urlParams) return
  
      const prefillValue = (() => {
        const val = urlParams.get(field.id)
        try {
          return typeof val === 'string' && val.startsWith('{') ? JSON.parse(val) : val
        } catch (e) {
          return val
        }
      })()
  
      const arrayPrefillValue = urlParams.getAll(field.id + '[]')
  
      if (typeof prefillValue === 'object' && prefillValue !== null) {
        formData[field.id] = { ...prefillValue }
      } else if (prefillValue !== null) {
        formData[field.id] = field.type === 'checkbox' ? this.parseBooleanValue(prefillValue) : prefillValue
      } else if (arrayPrefillValue.length > 0) {
        formData[field.id] = arrayPrefillValue
      }
    }
  
    parseBooleanValue(value) {
      return value === 'true' || value === '1'
    }
  
    handleDefaultPrefill(field, formData) {
      if (field.type === 'date' && field.prefill_today) {
        formData[field.id] = new Date().toISOString()
      } else if (field.type === 'matrix' && !formData[field.id]) {
        formData[field.id] = {...field.prefill}
      } else if (!(field.id in formData)) {
        formData[field.id] = field.prefill
      }
    }

    checkIfPublicFormPage() {
      if (import.meta.server) return false
      return window.location.pathname.startsWith('/forms/')
    }

    /**
     * Page validation
     */
    async validateCurrentPage(currentFields, isLastPage, formModeStrategy) {
      if (!formModeStrategy.validation.validateOnNextPage) {
        return true
      }

      try {
        this.formData.busy = true
        const fieldsToValidate = currentFields
          .filter(f => f.type !== 'payment')
          .map(f => f.id)

        // Validate non-payment fields first
        if (fieldsToValidate.length > 0) {
          await this.formData.validate('POST', `/forms/${this.form.slug}/answer`, {}, fieldsToValidate)
        }

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
}
  