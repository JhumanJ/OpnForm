// Hook function used by event listener
function hookLogEvent(eventName, eventData) {
  useAmplitude().logEvent(eventName, eventData)
}

export default defineNuxtPlugin((nuxtApp) => {
  // New v-track directive that only sets data attributes
  nuxtApp.vueApp.directive("track", {
    beforeMount(el, binding) {
      const modifiers = Object.keys(binding.modifiers)
      if (modifiers.length !== 1) {
        throw new Error(
          "Amplitude directive takes only one modifier which is the event name.",
        )
      }
      const eventName = modifiers[0]
      
      // Set data attributes instead of adding event listeners
      el.setAttribute('data-track-event', eventName)
      if (binding.value) {
        el.setAttribute('data-track-properties', JSON.stringify(binding.value))
      }
    },
    updated(el, binding) {
      // Update data attributes if the binding changes
      const modifiers = Object.keys(binding.modifiers)
      if (modifiers.length !== 1) {
        throw new Error(
          "Amplitude directive takes only one modifier which is the event name.",
        )
      }
      const eventName = modifiers[0]
      
      el.setAttribute('data-track-event', eventName)
      if (binding.value) {
        el.setAttribute('data-track-properties', JSON.stringify(binding.value))
      } else {
        el.removeAttribute('data-track-properties')
      }
    }
  })

  // Global click event listener using event delegation
  if (import.meta.client) {
    document.addEventListener('click', (event) => {
      // Find the closest element with tracking data
      const trackableElement = event.target.closest('[data-track-event]')
      
      if (trackableElement) {
        const eventName = trackableElement.getAttribute('data-track-event')
        const propertiesAttr = trackableElement.getAttribute('data-track-properties')
        
        let eventData = null
        if (propertiesAttr) {
          try {
            eventData = JSON.parse(propertiesAttr)
          } catch (e) {
            console.warn('Failed to parse tracking properties:', propertiesAttr, e)
          }
        }
        
        hookLogEvent(eventName, eventData)
      }
    })
  }
})
