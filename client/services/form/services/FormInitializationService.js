import { pendingSubmission } from "~/composables/forms/pendingSubmission.js"

export class FormInitializationService {
    constructor(form, formData, options = {}) {
      this.form = form
      this.formData = formData
      this.isPublicFormPage = this.checkIfPublicFormPage()
      this.formPageIndex = options.formPageIndex
      this.isInitialLoad = options.isInitialLoad
      this.stripeElements = options.stripeElements
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
     * Payment handling
     */
    async handlePayment(paymentBlock) {
      if (!this.stripeElements) return true
      const { state: stripeState, processPayment, isCardPopulated, isReadyForPayment } = this.stripeElements
      
      // Check if there's a payment block in the current step
      if (!paymentBlock) {
        return true // No payment needed for this step
      }
      
      // Skip if payment is already processed in the stripe state
      if (stripeState.intentId) {
        return true
      }
      
      // Skip if payment ID already exists in the form data
      const paymentFieldValue = this.formData.data()[paymentBlock.id]
      if (paymentFieldValue && typeof paymentFieldValue === 'string' && paymentFieldValue.startsWith('pi_')) {
        // If we have a valid payment intent ID in the form data, sync it to the stripe state
        stripeState.intentId = paymentFieldValue
        return true
      }
      
      // Check for the stripe object itself, not just the ready flag
      if (stripeState.isStripeInstanceReady && !stripeState.stripe) {
        stripeState.isStripeInstanceReady = false
      }
      
      // Only process payment if required or card has data
      const shouldProcessPayment = paymentBlock.required || isCardPopulated.value
      
      if (shouldProcessPayment) {
        // If not ready yet, try a brief wait
        if (!isReadyForPayment.value) {
          try {
            this.formData.busy = true
            
            // Just wait a second to see if state updates (it should be reactive now)
            await new Promise(resolve => setTimeout(resolve, 1000))
            
            // Check if we're ready now
            if (!isReadyForPayment.value) {
              // Provide detailed diagnostics
              let errorMsg = 'Payment system not ready. '
              const details = []
              
              if (!stripeState.stripeAccountId) {
                details.push('No Stripe account connected')
              }
              
              if (!stripeState.isStripeInstanceReady) {
                details.push('Stripe.js not initialized')
              }
              
              if (!stripeState.isCardElementReady) {
                details.push('Card element not initialized')
              }
              
              errorMsg += details.join(', ') + '. Please refresh and try again.'
              useAlert().error(errorMsg)
              return false
            }
          } catch (error) {
            return false
          } finally {
            this.formData.busy = false
          }
        }
        
        try {
          this.formData.busy = true
          const result = await processPayment(this.form.slug, paymentBlock.required)
          
          if (!result.success) {
            // Handle payment error
            if (result.error?.message) {
              this.formData.errors.set(paymentBlock.id, result.error.message)
              useAlert().error(result.error.message)
            } else {
              useAlert().error('Payment processing failed. Please try again.')
            }
            return false
          }
          
          // Payment successful
          if (result.paymentIntent?.status === 'succeeded') {
            useAlert().success('Thank you! Your payment is successful.')
            return true
          }
          
          // Fallback error
          useAlert().error('Something went wrong with the payment. Please try again.')
          return false
        } catch (error) {
          useAlert().error(error?.message || 'Payment failed')
          return false
        } finally {
          this.formData.busy = false
        }
      }
      
      return true // Payment not required or no card data
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
  