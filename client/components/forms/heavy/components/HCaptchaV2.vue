<template>
  <div class="hcaptcha-container">
    <div ref="hcaptchaContainer" />
  </div>
</template>

<script setup>
const props = defineProps({
  sitekey: {
    type: String,
    required: true
  },
  theme: {
    type: String,
    default: 'light'
  },
  language: {
    type: String,
    default: 'en'
  }
})

const emit = defineEmits(['verify', 'expired', 'opened', 'closed'])
const hcaptchaContainer = ref(null)
let widgetId = null

// Global script loading state
const SCRIPT_ID = 'hcaptcha-script'
let scriptLoadPromise = null

// Add this cleanup function at the script level
const cleanupHcaptcha = () => {
  // Remove all hCaptcha iframes
  document.querySelectorAll('iframe[src*="hcaptcha.com"]').forEach(iframe => {
    iframe.remove()
  })

  // Remove all hCaptcha scripts
  document.querySelectorAll('script[src*="hcaptcha.com"]').forEach(script => {
    script.remove()
  })

  // Remove specific script
  const script = document.getElementById(SCRIPT_ID)
  if (script) {
    script.remove()
  }

  // Remove hCaptcha styles
  document.querySelectorAll('style[data-emotion]').forEach(style => {
    style.remove()
  })

  // Clean up global variables
  if (window.hcaptcha) {
    delete window.hcaptcha
  }

  // Clean up any potential callbacks
  Object.keys(window).forEach(key => {
    if (key.startsWith('hcaptchaOnLoad_')) {
      delete window[key]
    }
  })

  scriptLoadPromise = null
}

const loadHcaptchaScript = () => {
  if (scriptLoadPromise) return scriptLoadPromise

  // Clean up before loading new script
  cleanupHcaptcha()

  scriptLoadPromise = new Promise((resolve, reject) => {
    // If hcaptcha is already available and ready, use it
    if (window.hcaptcha?.render) {
      resolve(window.hcaptcha)
      return
    }

    // Create a unique callback name
    const callbackName = `hcaptchaOnLoad_${Date.now()}`

    // Create the script
    const script = document.createElement('script')
    script.id = SCRIPT_ID
    script.src = `https://js.hcaptcha.com/1/api.js?render=explicit&onload=${callbackName}&recaptchacompat=off`
    script.async = true
    script.defer = true

    let timeoutId = null

    // Set up the callback before adding the script
    window[callbackName] = () => {
      if (timeoutId) clearTimeout(timeoutId)
      if (window.hcaptcha?.render) {
        resolve(window.hcaptcha)
        delete window[callbackName]
      } else {
        reject(new Error('hCaptcha failed to initialize'))
      }
    }

    script.onerror = (error) => {
      if (timeoutId) clearTimeout(timeoutId)
      delete window[callbackName]
      scriptLoadPromise = null
      reject(error)
    }

    timeoutId = setTimeout(() => {
      delete window[callbackName]
      scriptLoadPromise = null
      reject(new Error('hCaptcha script load timeout'))
    }, 10000)

    document.head.appendChild(script)
  })

  return scriptLoadPromise
}

const renderHcaptcha = async () => {
  try {
    // Clear any existing content first
    if (hcaptchaContainer.value) {
      hcaptchaContainer.value.innerHTML = ''
    }

    const hcaptcha = await loadHcaptchaScript()
    
    // Double check container still exists after async operation
    if (!hcaptchaContainer.value) return

    // Render new widget
    widgetId = hcaptcha.render(hcaptchaContainer.value, {
      sitekey: props.sitekey,
      theme: props.theme,
      hl: props.language,
      'callback': (token) => emit('verify', token),
      'expired-callback': () => emit('expired'),
      'error-callback': () => {
        if (widgetId !== null) {
          hcaptcha.reset(widgetId)
        }
      },
      'open-callback': () => emit('opened'),
      'close-callback': () => emit('closed')
    })
  } catch {
    scriptLoadPromise = null // Reset promise on error
  }
}

onMounted(() => {
  renderHcaptcha()
})

onBeforeUnmount(() => {
  // Clean up widget and reset state
  if (window.hcaptcha && widgetId !== null) {
    try {
      window.hcaptcha.remove(widgetId)
    } catch {
      // Silently handle error
    }
  }
  
  cleanupHcaptcha()
  
  if (hcaptchaContainer.value) {
    hcaptchaContainer.value.innerHTML = ''
  }
  
  widgetId = null
})

// Expose reset method that properly reloads the captcha
defineExpose({
  reset: async () => {
    if (window.hcaptcha && widgetId !== null) {
      try {
        // Use the official API to reset the captcha widget
        window.hcaptcha.reset(widgetId)
        return true
      } catch (error) {
        console.error('Error resetting hCaptcha, falling back to re-render', error)
      }
    }
    
    // Fall back to full re-render if reset fails or hcaptcha isn't available
    cleanupHcaptcha()
    await renderHcaptcha()
  }
})
</script> 