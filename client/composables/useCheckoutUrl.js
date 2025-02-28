export const useCheckoutUrl = (name, email, plan, yearly, currency) => {
  return computed(() => {
    const params = {
      plan,
      yearly: yearly.toString(),
      currency,
      name,
      email
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