// Log event function used to log event. Can be used when not using the directive.
const logEvent = function (eventName, eventData) {
  if (!window.amplitude) return
  if (eventData && typeof eventData !== 'object') {
    throw new Error('Amplitude event value must be an object.')
  }

  console.log('in', window.config.production)
  if (!window.config.production) {
    console.log('[DEBUG] Amplitude logged event:', eventName, eventData)
  } else {
    window.amplitude.getInstance().logEvent(eventName, eventData)
  }
}

// Hook function used by event listener
function hookLogEvent (binding) {
  const modifiers = Object.keys(binding.modifiers)
  if (modifiers.length !== 1) {
    throw new Error('Amplitude directive takes only one modifier which is the event name.')
  }
  const eventName = modifiers[0]

  logEvent(eventName, binding.value)
}

// Used in vue-plugins.js
export function registerLogEventOnApp (app) {
  app.config.globalProperties.$logEvent = logEvent

  // Register directive to log event
  const registeredListeners = {}
  app.directive('track', {
    beforeMount (el, binding, vnode) {
      registeredListeners[el] = () => {
        hookLogEvent(binding)
      }
      el.addEventListener('click', registeredListeners[el])
    }
  })
}
