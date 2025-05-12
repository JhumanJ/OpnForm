import { computed, unref } from 'vue'

export const useCheckoutUrl = (name, email, plan, yearly, currency) => {
  return computed(() => {
    // Unwrap refs if they are passed
    const nameValue = unref(name)
    const emailValue = unref(email)
    const planValue = unref(plan)
    const yearlyValue = unref(yearly)
    const currencyValue = unref(currency)

    const params = {
      plan: planValue,
      yearly: yearlyValue.toString(),
      currency: currencyValue,
      name: nameValue,
      email: emailValue
    }

    // Get trial duration if exists - only in client side
    if (import.meta.client) {
      const urlParams = new URLSearchParams(window.location.search)
      const trialDuration = urlParams.get('trial_duration')
      if (trialDuration) {
        params.trial_duration = trialDuration
        // Keep the amplitude event
        useAmplitude().logEvent('extended_trial_used', { duration: trialDuration })
      }
    }

    // Filter out empty params
    const filteredParams = Object.fromEntries(
       
      Object.entries(params).filter(([_, value]) => value !== null && value !== undefined && value !== '')
    )

    return {
      name: 'redirect-checkout',
      query: filteredParams
    }
  })
} 