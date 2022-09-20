import Vue from 'vue'

// Log event function used to log event. Can be used when not using the directive.
Vue.prototype.$logEvent = function (eventName, eventData) {
  if (!window.amplitude) return
  if (eventData && typeof eventData !== 'object') {
    throw new Error('Amplitude event value must be an object.')
  }

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

  Vue.prototype.$logEvent(eventName, binding.value)
}

// Register directive to log event
const registeredListeners = {}
Vue.directive('track', {
  bind (el, binding, vnode) {
    registeredListeners[el] = () => {
      hookLogEvent(binding)
    }
    el.addEventListener('click', registeredListeners[el])
  }
})
