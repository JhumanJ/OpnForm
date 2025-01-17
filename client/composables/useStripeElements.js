let state = null // singleton pattern

export const useStripeElements = () => {
  if (!state) {
    state = ref({
      isLoaded: false,
      stripe: null,
      elements: null,
      card: null,
      cardHolderName: '',
      cardHolderEmail: ''
    })
  }

  const processPayment = async (formSlug) => {
    if (!state.value.stripe || !state.value.elements) {
      throw new Error('Stripe not initialized')
    }

    await opnFetch('/forms/' + formSlug + '/payment-intent').then(async (responseIntent) => {
      if(responseIntent?.type === 'success') {
        const intentSecret = responseIntent?.intent?.secret
        console.log('intentSecret', intentSecret)

        const result = await state.value.stripe.confirmCardPayment(intentSecret, {
          payment_method: {
            card: state.value.card,
            billing_details: {
              name: state.value.cardHolderName,
              email: state.value.cardHolderEmail
            },
          }
        })
        console.log('result', result)
    
        if (result.error) {
          throw new Error(result.error.message)
        }
    
        return result.paymentIntent
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