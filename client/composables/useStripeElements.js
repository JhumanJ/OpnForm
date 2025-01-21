let state = null // singleton pattern

export const useStripeElements = () => {
  if (!state) {
    state = ref({
      isLoaded: false,
      stripe: null,
      elements: null,
      card: null,
      cardHolderName: '',
      cardHolderEmail: '',
      intentId: null
    })
  }

  const processPayment = async (formSlug) => {
    if (!state.value.stripe || !state.value.elements) {
      throw new Error('Stripe not initialized')
    }

    if(!state.value.cardHolderName) {
      return { error: { message: 'Card holder name is required' } }
    }
    if(!state.value.cardHolderEmail) {
      return { error: { message: 'Billing email address is required' } }
    }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(state.value.cardHolderEmail)) {
      return { error: { message: 'Invalid billing email address' } }
    }

    return await opnFetch('/forms/' + formSlug + '/payment-intent').then(async (responseIntent) => {
      if (responseIntent?.type === 'success') {
        state.value.intentId = responseIntent?.intent?.id
        const intentSecret = responseIntent?.intent?.secret
        const stripeInstance = state.value?.elements?.instance
        
        const result = await stripeInstance.confirmCardPayment(intentSecret, {
          payment_method: {
            card: state.value.card,
            billing_details: {
              name: state.value.cardHolderName,
              email: state.value.cardHolderEmail
            },
          },
          receipt_email: state.value.cardHolderEmail,
        })
        return result
      } else { 
        useAlert().error(responseIntent.message)
      }
    })
  }

  return {
    state,
    processPayment
  }
}