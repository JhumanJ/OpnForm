/**
 * @fileoverview Service dedicated to handling payment processing logic, specifically interacting
 * with the Stripe payment provider via the useStripeElements composable.
 */
import { computed } from 'vue'
import { useStripeElements } from '~/composables/useStripeElements'

// services/form/services/FormPaymentService.js
export class FormPaymentService {
  constructor(formConfig, formData) {
    this.formConfig = formConfig
    this.formData = formData

    // Use computed to reactively initialize Stripe Elements
    this.reactiveStripeElements = computed(() => {
      const hasPaymentBlock = this.formConfig.properties?.some(prop => prop && prop.type === 'payment');
      if (hasPaymentBlock) {
          console.log('FormPaymentService: Payment block detected, initializing Stripe Elements reactively.');
          return useStripeElements();
      } else {
          console.log('FormPaymentService: No payment block detected, Stripe Elements not initialized.');
          return null;
      }
    });
  }

  // --- Getters now access the computed property's value ---
  get stripeElementsInstance() {
      return this.reactiveStripeElements.value;
  }
  
  get isPaymentReady() {
    // Access the reactive property from the computed value
    return this.stripeElementsInstance?.isReadyForPayment?.value ?? false
  }

  get hasPaymentIntent() {
    // Access the reactive state from the computed value
    return !!this.stripeElementsInstance?.state?.intentId?.value
  }

  isCardPopulated() {
    // Access the reactive property from the computed value
    return this.stripeElementsInstance?.isCardPopulated?.value ?? false
  }

  // --- Methods now use stripeElementsInstance ---
  async processPayment(paymentBlock) {
    // If there's no payment block on the current page, payment is trivially successful
    if (!paymentBlock) return { success: true }
    
    const stripeInstance = this.stripeElementsInstance; // Get current instance
    
    // If Stripe Elements are not initialized, consider it success
    if (!stripeInstance) {
        console.log('Payment processing skipped: Stripe Elements not initialized for this form configuration.');
        return { success: true };
    }

    // Skip if payment is already processed (e.g., navigating back after successful payment)
    if (this.hasPaymentIntent) { // uses getter which accesses stripeElementsInstance
        console.log('Payment already processed (intent found).')
        return { success: true }
    }

    // Check if the form data for the payment block already contains a Payment Intent ID
    const paymentFieldValue = this.formData.data()[paymentBlock.id]
    if (paymentFieldValue && typeof paymentFieldValue === 'string' && paymentFieldValue.startsWith('pi_')) {
      console.log('Payment intent ID found in form data, associating with Stripe elements.')
      // Ensure state and intentId are reactive refs if needed
      if(stripeInstance.state?.intentId) {
        stripeInstance.state.intentId.value = paymentFieldValue
      } else {
          console.warn('stripeInstance.state.intentId not available to set existing payment intent.')
      }
      return { success: true }
    }

    // Determine if payment processing is required
    // It's required if the block mandates it OR if the user has started filling the card details
    const shouldProcessPayment = paymentBlock.required || this.isCardPopulated() // uses getter

    if (shouldProcessPayment) {
      console.log(`Processing payment. Required: ${paymentBlock.required}, Card Populated: ${this.isCardPopulated()}`)
      if (!this.isPaymentReady) { // uses getter
        console.log('Waiting for payment system readiness...')
        try {
            await this.waitForPaymentReadiness() // uses method which accesses stripeElementsInstance
            console.log('Payment system ready.')
        } catch (error) {
            console.error(error.message)
            // Return a structured error compatible with FormManager expectations
            return { success: false, error: { message: error.message || 'Payment system failed to initialize.' } }
        }
      }

      // Ensure the processPayment method exists on the composable
      if (typeof stripeInstance.processPayment !== 'function') {
        console.error('useStripeElements does not provide a processPayment function.')
        return { success: false, error: { message: 'Payment processing unavailable.' } }
      }

      // Call the payment processing function from the composable
      console.log('Calling stripeInstance.processPayment...')
      const result = await stripeInstance.processPayment(this.formConfig.slug, paymentBlock.required)
      console.log('Payment processing result:', result)
      // The result structure should include { success: boolean, error?: { message: string } }
      return result 

    } else {
        console.log('Payment processing not required for this block.')
        // If payment is not required and card is not populated, treat as success
        return { success: true }
    }
  }

  async waitForPaymentReadiness(timeoutMs = 5000, intervalMs = 100) {
    return new Promise((resolve, reject) => {
      const stripeInstance = this.stripeElementsInstance; // Get current instance
      // If Stripe Elements are not initialized, resolve immediately
      if (!stripeInstance) {
          return resolve();
      }
      
      const startTime = Date.now();

      const checkReadiness = () => {
        // Check readiness via the getter which uses the instance
        if (this.isPaymentReady) { 
          resolve()
        } else if (Date.now() - startTime > timeoutMs) {
            reject(new Error('Payment system initialization timeout'))
        } else {
          setTimeout(checkReadiness, intervalMs)
        }
      }

      checkReadiness()
    })
  }
} 