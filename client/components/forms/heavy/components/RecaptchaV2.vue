<template>
  <div class="recaptcha-container">
    <div ref="recaptchaContainer" />
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
const recaptchaContainer = ref(null)
let widgetId = null

// Global script loading state
const SCRIPT_ID = 'recaptcha-script'
let scriptLoadPromise = null

// Add cleanup function similar to hCaptcha
const cleanupRecaptcha = () => {
  // Remove all reCAPTCHA iframes
  document.querySelectorAll('iframe[src*="google.com/recaptcha"]').forEach(iframe => {
    iframe.remove()
  })

  // Remove all reCAPTCHA scripts
  document.querySelectorAll('script[src*="google.com/recaptcha"]').forEach(script => {
    script.remove()
  })

  // Remove specific script
  const script = document.getElementById(SCRIPT_ID)
  if (script) {
    script.remove()
  }

  // Clean up global variables
  if (window.grecaptcha) {
    delete window.grecaptcha
  }

  scriptLoadPromise = null
}

const loadRecaptchaScript = () => {
  if (scriptLoadPromise) return scriptLoadPromise

  // Clean up before loading new script
  cleanupRecaptcha()

  scriptLoadPromise = new Promise((resolve, reject) => {
    // If grecaptcha is already available and ready, use it
    if (window.grecaptcha?.render) {
      resolve(window.grecaptcha)
      return
    }

    const script = document.createElement('script')
    script.id = SCRIPT_ID
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit'
    script.async = true
    script.defer = true

    let timeoutId = null

    script.onload = () => {
      const checkGrecaptcha = () => {
        if (window.grecaptcha?.render) {
          if (timeoutId) clearTimeout(timeoutId)
          resolve(window.grecaptcha)
        } else {
          setTimeout(checkGrecaptcha, 100)
        }
      }
      checkGrecaptcha()
    }

    script.onerror = (error) => {
      if (timeoutId) clearTimeout(timeoutId)
      scriptLoadPromise = null
      reject(error)
    }

    timeoutId = setTimeout(() => {
      scriptLoadPromise = null
      reject(new Error('reCAPTCHA script load timeout'))
    }, 10000)

    document.head.appendChild(script)
  })

  return scriptLoadPromise
}

const renderRecaptcha = async () => {
  try {
    // Clear any existing content first
    if (recaptchaContainer.value) {
      recaptchaContainer.value.innerHTML = ''
    }

    const grecaptcha = await loadRecaptchaScript()
    
    // Double check container still exists after async operation
    if (!recaptchaContainer.value) return

    // Render new widget
    widgetId = grecaptcha.render(recaptchaContainer.value, {
      sitekey: props.sitekey,
      theme: props.theme,
      hl: props.language,
      callback: (token) => emit('verify', token),
      'expired-callback': () => emit('expired'),
      'error-callback': () => {
        if (widgetId !== null) {
          grecaptcha.reset(widgetId)
        }
      }
    })
  } catch {
    scriptLoadPromise = null // Reset promise on error
  }
}

onMounted(() => {
  renderRecaptcha()
})

onBeforeUnmount(() => {
  // Clean up widget and reset state
  if (window.grecaptcha && widgetId !== null) {
    try {
      window.grecaptcha.reset(widgetId)
    } catch {
      // Silently handle error
    }
  }
  
  cleanupRecaptcha()
  
  if (recaptchaContainer.value) {
    recaptchaContainer.value.innerHTML = ''
  }
  
  widgetId = null
})

// Expose reset method that properly reloads the captcha
defineExpose({
  reset: async () => {
    if (window.grecaptcha && widgetId !== null) {
      try {
        // Try simple reset first
        window.grecaptcha.reset(widgetId)
        return true
      } catch (error) {
        console.error('Error resetting reCAPTCHA, falling back to re-render', error)
      }
    }
    
    // If simple reset fails or no widget exists, do a full reload
    cleanupRecaptcha()
    await renderRecaptcha()
  }
})
</script>