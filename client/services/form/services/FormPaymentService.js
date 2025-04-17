export class FormPaymentService {
    constructor(form, formData, stripeElements) {
      this.form = form
      this.formData = formData
      this.stripeElements = stripeElements
    }
  
    /**
     * Check if payment system is ready
     */
    get isPaymentReady() {
      if (!this.stripeElements) return false
      const { isReadyForPayment } = this.stripeElements
      return isReadyForPayment.value
    }
  
    /**
     * Check if payment intent exists
     */
    get hasPaymentIntent() {
      if (!this.stripeElements) return false
      const { state } = this.stripeElements
      return !!state.intentId
    }
  
    /**
     * Check if card is populated with data
     */
    isCardPopulated() {
      if (!this.stripeElements) return false
      const { isCardPopulated } = this.stripeElements
      return isCardPopulated.value
    }
  
    /**
     * Process payment for a given payment block
     */
    async processPayment(paymentBlock) {
      if (!paymentBlock) return { success: true }
      if (!this.stripeElements) return { success: true }

      const { state: stripeState } = this.stripeElements
  
      // Skip if payment is already processed
      if (this.hasPaymentIntent) return { success: true }
  
      // Check for existing payment intent in form data
      const paymentFieldValue = this.formData.data()[paymentBlock.id]
      if (paymentFieldValue && typeof paymentFieldValue === 'string' && paymentFieldValue.startsWith('pi_')) {
        this.stripeElements.state.intentId = paymentFieldValue
        return { success: true }
      }

      // Check for the stripe object itself, not just the ready flag
      if (stripeState.isStripeInstanceReady && !stripeState.stripe) {
        stripeState.isStripeInstanceReady = false
      }
  
      // Process payment if required or if card has data
      const shouldProcessPayment = paymentBlock.required || this.isCardPopulated()
  
      if (shouldProcessPayment) {
        if (!this.isPaymentReady) {
          try {
            this.formData.busy = true
            await this.waitForPaymentReadiness()

            // Check if we're ready now
            if (!this.isPaymentReady) {
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
              return { success: false }
            }
          } catch (error) {
            return { success: false }
          } finally {
            this.formData.busy = false
          }
        }
  
        try {
          this.formData.busy = true
          const { processPayment } = this.stripeElements
          const result = await processPayment(this.form.slug, paymentBlock.required)
          
          if (!result.success) {
            // Handle payment error
            if (result.error?.message) {
              this.formData.errors.set(paymentBlock.id, result.error.message)
              useAlert().error(result.error.message)
            } else {
              useAlert().error('Payment processing failed. Please try again.')
            }
            return { success: false }
          }
          
          // Payment successful
          if (result.paymentIntent?.status === 'succeeded') {
            useAlert().success('Thank you! Your payment is successful.')
            return { success: true }
          }
          
          // Fallback error
          useAlert().error('Something went wrong with the payment. Please try again.')
          return { success: false }
        } catch (error) {
          useAlert().error(error?.message || 'Payment failed')
          return { success: false }
        } finally {
          this.formData.busy = false
        }
      }
  
      return { success: true }
    }
  
    /**
     * Wait for payment system to be ready
     */
    async waitForPaymentReadiness() {
      return new Promise((resolve, reject) => {
        const timeout = setTimeout(() => {
          reject(new Error('Payment system initialization timeout'))
        }, 3000)
  
        const checkReadiness = () => {
          if (this.isPaymentReady) {
            clearTimeout(timeout)
            resolve()
          } else {
            setTimeout(checkReadiness, 100)
          }
        }
  
        checkReadiness()
      })
    }
  
    /**
     * Reset payment state
     */
    reset() {
      if (this.stripeElements) {
        this.stripeElements.state.intentId = null
      }
    }
  }
  