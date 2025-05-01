import { computed, reactive } from 'vue'
import { useI18n } from '#imports'

/**
 * Creates a Stripe elements instance with state management
 * @returns {Object} Stripe elements API with state and methods
 */
export const createStripeElements = () => {
  // Get the translation function
  const { t } = useI18n()

  // Use reactive for the state to ensure changes propagate
  const state = reactive({
    // Loading states
    isLoadingAccount: false,
    hasAccountLoadingError: false,
    isStripeInstanceReady: false,
    isCardElementReady: false,
    
    // Core Stripe objects
    stripe: null,
    elements: null,
    card: null,
    
    // Form data
    cardHolderName: '',
    cardHolderEmail: '',
    
    // Account & payment state
    stripeAccountId: null,
    intentId: null,
    showPreviewMessage: false,
    errorMessage: ''
  })

  // Computed properties
  const isReadyForPayment = computed(() => {
    return state.isStripeInstanceReady && 
           state.isCardElementReady && 
           state.stripeAccountId
  })

  const isCardPopulated = computed(() => {
    return state.card && !state.card._empty
  })

  /**
   * Resets the Stripe state to its initial values
   */
  const resetStripeState = () => {
    state.isLoadingAccount = false
    state.hasAccountLoadingError = false
    state.isStripeInstanceReady = false
    state.isCardElementReady = false
    state.stripe = null
    state.elements = null
    state.card = null
    state.intentId = null
    state.showPreviewMessage = false
    state.stripeAccountId = null
    state.errorMessage = ''
  }

  /**
   * Fetches the Stripe account ID required for connecting to the proper account
   * @param {string} formSlug - The slug of the form
   * @param {string} providerId - The OAuth provider ID
   * @param {boolean} isEditorPreview - Whether this is in editor preview mode
   * @returns {Promise<Object>} - Object containing success/error information
   */
  const prepareStripeState = async (formSlug, providerId, isEditorPreview = false) => {
    if (!formSlug || !providerId) {
      resetStripeState()
      return { success: false, message: t('forms.payment.errors.missingFormOrProvider') }
    }
    
    resetStripeState()
    state.isLoadingAccount = true
    
    try {
      const fetchOptions = {}
      if (isEditorPreview) {
        fetchOptions.query = { oauth_provider_id: providerId }
      }
      
      const response = await opnFetch(`/forms/${formSlug}/stripe-connect/get-account`, fetchOptions)

      if (response?.type === 'success' && response?.stripeAccount) {
        // Explicitly set the account ID in state
        state.stripeAccountId = response.stripeAccount
        state.isLoadingAccount = false
        
        // We'll rely on the StripeElements component to create the Stripe instance
        // Don't try to create it here
        
        return { success: true, accountId: response.stripeAccount }
      } else {
        state.hasAccountLoadingError = true
        state.isLoadingAccount = false
        
        if (response?.message?.includes('save the form and try again')) {
          state.showPreviewMessage = true
        }
        
        state.errorMessage = response?.message || t('forms.payment.errors.failedAccountDetails')
        return { 
          success: false, 
          message: state.errorMessage,
          requiresSave: state.showPreviewMessage
        }
      }
    } catch (error) {
      state.hasAccountLoadingError = true
      state.isLoadingAccount = false
      
      const message = error?.data?.message || t('forms.payment.errors.setupError')
      
      if (message.includes('save the form and try again')) {
        state.showPreviewMessage = true
      }
      
      state.errorMessage = message
      return { 
        success: false, 
        message: state.errorMessage,
        requiresSave: state.showPreviewMessage
      }
    }
  }

  /**
   * Sets the Stripe instance in the state
   * @param {Object} instance - The Stripe instance from vue-stripe-js
   */
  const setStripeInstance = (instance) => {
    // Check if the instance is actually a Stripe instance by looking for known methods
    const isValidStripeInstance = instance && 
      typeof instance === 'object' && 
      typeof instance.confirmCardPayment === 'function' &&
      typeof instance.createToken === 'function'
    
    if (instance && isValidStripeInstance) {
      // Only set if the instance is different to avoid unnecessary updates
      if (state.stripe !== instance) {
        state.stripe = instance
        state.isStripeInstanceReady = true
      }
    } else {
      state.stripe = null
      state.isStripeInstanceReady = false
    }
  }

  /**
   * Sets the Elements instance in the state
   * @param {Object} elementsInstance - The Elements instance from vue-stripe-js
   */
  const setElementsInstance = (elementsInstance) => {
    if (elementsInstance) {
      state.elements = elementsInstance
    }
  }
  
  /**
   * Sets the Card Element in the state
   * @param {Object} cardElement - The Card Element instance
   */
  const setCardElement = (cardElement) => {
    if (cardElement) {
      state.card = cardElement
      state.isCardElementReady = true
    } else {
      state.card = null
      state.isCardElementReady = false
    }
  }
  
  /**
   * Sets the billing details in the state
   * @param {Object} details - The billing details object {name, email}
   */
  const setBillingDetails = ({ name, email }) => {
    if (name !== undefined) state.cardHolderName = name
    if (email !== undefined) state.cardHolderEmail = email
  }

  /**
   * Processes a payment using the Stripe API
   * @param {string} formSlug - The slug of the form
   * @param {boolean} isRequired - Whether payment is required to proceed
   * @returns {Promise<Object>} - Object containing payment result or error
   */
  const processPayment = async (formSlug, isRequired = true) => {
    // Check if Stripe is fully initialized
    if (!isReadyForPayment.value) {
      return { 
        success: false,
        error: { message: t('forms.payment.errors.systemNotReady') } 
      }
    }

    // Check if the stripe instance exists
    if (!state.stripe) {
      return {
        success: false,
        error: { message: t('forms.payment.errors.misconfigured') }
      }
    }
    
    // Additional validation for card
    if (!state.card) {
      return {
        success: false,
        error: { message: t('forms.payment.errors.notFullyReady') }
      }
    }
    
    // Check if payment is required but card is empty
    if (isRequired && state.card._empty) {
      return { 
        success: false,
        error: { message: t('forms.payment.errors.paymentRequired') } 
      }
    }
    
    // Only validate billing details if payment is required or card has data
    if (isRequired || !state.card._empty) {
      // Validate card holder name
      if (!state.cardHolderName) {
        return { 
          success: false,
          error: { message: t('forms.payment.errors.nameRequired') } 
        }
      }
      
      // Validate billing email
      if (!state.cardHolderEmail) {
        return { 
          success: false,
          error: { message: t('forms.payment.errors.emailRequired') } 
        }
      }
      
      // Validate email format
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.cardHolderEmail)) {
        return { 
          success: false,
          error: { message: t('forms.payment.errors.invalidEmail') } 
        }
      }
    }

    try {
      // Get payment intent from server
      const responseIntent = await opnFetch('/forms/' + formSlug + '/stripe-connect/payment-intent')
      
      if (responseIntent?.type === 'success') {
        const intentSecret = responseIntent?.intent?.secret
        
        // Confirm card payment with Stripe
        const result = await state.stripe.confirmCardPayment(intentSecret, {
          payment_method: {
            card: state.card,
            billing_details: {
              name: state.cardHolderName,
              email: state.cardHolderEmail
            },
          },
          receipt_email: state.cardHolderEmail,
        })
        
        // Store payment intent ID on success
        if (result?.paymentIntent?.status === 'succeeded') {
          state.intentId = result.paymentIntent.id
        }
        
        return {
          success: result?.paymentIntent?.status === 'succeeded',
          ...result
        }
      } else {
        return { 
          success: false,
          error: { message: responseIntent?.message || t('forms.payment.errors.failedIntent') } 
        }
      }
    } catch (error) {
      // Include more details about the error
      const errorMessage = error?.message || t('forms.payment.errors.processingFailed')
      const errorType = error?.type || 'unknown'
      const errorCode = error?.code || 'unknown'
      
      return { 
        success: false,
        error: { 
          message: errorMessage,
          type: errorType,
          code: errorCode
        } 
      }
    }
  }

  const stripeElements = {
    state,
    isReadyForPayment,
    isCardPopulated,
    processPayment,
    resetStripeState,
    prepareStripeState,
    setStripeInstance,
    setElementsInstance,
    setCardElement,
    setBillingDetails
  }

  // Return the API
  return stripeElements
}