import amplitude from 'amplitude-js'
import config from '~/opnform.config.js'

export default () => {
  const amplitudeClient = config.amplitude_code ? amplitude.getInstance().init(config.amplitude_code) : null;

  const logEvent = function (eventName, eventData) {
    if (!config.production || !amplitudeClient) {
      console.log('[DEBUG] Amplitude logged event:', eventName, eventData)
    }

    if (eventData && typeof eventData !== 'object') {
      throw new Error('Amplitude event value must be an object.')
    }

    amplitudeClient.logEvent(eventName, eventData)
  }

  const setUser = function (user) {
    amplitudeClient.setUserId(user.id)
    amplitudeClient.setUserProperties({
      email: this.user.email,
      subscribed: this.user.is_subscribed,
      enterprise_subscription: this.user.has_enterprise_subscription
    })
  }


  return {
    logEvent,
    setUser,
    amplitude: amplitudeClient
  }
}
