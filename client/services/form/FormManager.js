import { reactive } from 'vue'
import { FormStructureService } from './services/FormStructureService'
import { FormInitializationService } from './services/FormInitializationService'
import { FormValidationService } from './services/FormValidationService'
import { FormSubmissionService } from './services/FormSubmissionService'
import { FormPaymentService } from './services/FormPaymentService'
import { useForm } from '~/composables/useForm'

export class FormManager {
  constructor(formConfig) {
    this.config = formConfig
    this._isInitialized = false
    
    // Initialize form data with empty values for all fields
    const initialData = {}
    if (formConfig.properties) {
      formConfig.properties.forEach(field => {
        if (field.id) {
          initialData[field.id] = null
        }
      })
    }
    
    this.form = useForm(initialData)
    this.state = reactive({
      currentPage: 0,
      isSubmitted: false,
      isProcessingPayment: false,
      completionTime: 0
    })

    // Initialize services
    this.structureService = new FormStructureService(formConfig)
    this.initService = new FormInitializationService(formConfig, this.form)
    this.validationService = new FormValidationService(formConfig, this.form)
    this.submissionService = new FormSubmissionService(formConfig, this.form)
    this.paymentService = new FormPaymentService(formConfig, this.form)
  }

  // Public API
  async initialize(options = {}) {
    if (this._isInitialized) {
      console.log('FormManager already initialized, skipping')
      return true
    }

    console.log('FormManager initializing with options:', options)
    try {
      await this.initService.initialize(options)
      this._isInitialized = true
      console.log('FormManager initialized successfully')
      return true
    } catch (error) {
      console.error('FormManager initialization failed:', error)
      throw error
    }
  }

  // Form data access
  get data() {
    return this.form.data()
  }

  get errors() {
    return this.form.errors
  }

  get isInitialized() {
    return this._isInitialized
  }

  // Structure methods
  getCurrentPageFields() {
    return this.structureService.getPageFields(this.state.currentPage)
  }

  get pageCount() {
    return this.structureService.getPageCount()
  }

  get isLastPage() {
    return this.structureService.isLastPage(this.state.currentPage)
  }

  // Navigation
  async nextPage() {
    const currentFields = this.getCurrentPageFields()
    const fieldsToValidate = currentFields
      .filter(f => f.type !== 'payment')
      .map(f => f.id)

    try {
      this.state.isProcessingPayment = true

      // Validate non-payment fields first
      if (this.config.validateOnNextPage && fieldsToValidate.length > 0) {
        await this.validationService.validateFields(fieldsToValidate)
      }

      // Process payment if present on this page
      const paymentBlock = this.structureService.getPaymentBlock(this.state.currentPage)
      if (paymentBlock) {
        const paymentResult = await this.paymentService.processPayment(paymentBlock)
        if (!paymentResult.success) {
          // Handle payment error
          if (paymentResult.error?.message) {
            this.form.errors.set(paymentBlock.id, paymentResult.error.message)
          }
          return false
        }
      }

      // Move to next page if not the last
      if (!this.isLastPage) {
        this.state.currentPage++
      }

      return true
    } catch (error) {
      // Handle validation errors
      return false
    } finally {
      this.state.isProcessingPayment = false
    }
  }

  previousPage() {
    if (this.state.currentPage > 0) {
      this.state.currentPage--
    }
  }

  // Submission
  async submit() {
    if (!await this.nextPage()) {
      return false
    }

    if (!this.isLastPage) {
      return false
    }

    return this.submissionService.submit({
      completionTime: this.state.completionTime
    })
  }

  setCompletionTime(time) {
    this.state.completionTime = time
  }
}
