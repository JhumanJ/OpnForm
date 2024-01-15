
// Hook function used by event listener
function hookLogEvent (binding) {
  const modifiers = Object.keys(binding.modifiers)
  if (modifiers.length !== 1) {
    throw new Error('Amplitude directive takes only one modifier which is the event name.')
  }
  const eventName = modifiers[0]

  useAmplitude().logEvent(eventName, binding.value)
}

export default defineNuxtPlugin(nuxtApp => {
  // Doing something with nuxtApp
  const registeredListeners = {}
  nuxtApp.vueApp.directive('track', {
    beforeMount (el, binding, vnode) {
      registeredListeners[el] = () => {
        hookLogEvent(binding)
      }
      el.addEventListener('click', registeredListeners[el])
    }
  })
})
