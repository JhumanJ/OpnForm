<template>
  <div>
    <div v-if="showCaptcha && isSiteKeyAvailable">
      <RecaptchaV2
        v-if="provider === 'recaptcha'"
        :key="`recaptcha-${componentKey}`"
        ref="captchaRef"
        :sitekey="recaptchaSiteKey"
        :theme="darkMode ? 'dark' : 'light'"
        :language="language"
        @verify="onCaptchaVerify"
        @expired="onCaptchaExpired"
        @opened="onCaptchaOpen"
        @closed="onCaptchaClose"
      />
      <HCaptchaV2
        v-else
        :key="`hcaptcha-${componentKey}`"
        ref="captchaRef"
        :sitekey="hCaptchaSiteKey"
        :theme="darkMode ? 'dark' : 'light'"
        :language="language"
        @verify="onCaptchaVerify"
        @expired="onCaptchaExpired"
        @opened="onCaptchaOpen"
        @closed="onCaptchaClose"
      />
    </div>
    <has-error
      :form="form"
      :field-id="formFieldName"
    />
  </div>
</template>

<script setup>
import HCaptchaV2 from './HCaptchaV2.vue'
import RecaptchaV2 from './RecaptchaV2.vue'

const props = defineProps({
  provider: {
    type: String,
    required: true,
    validator: (value) => ['recaptcha', 'hcaptcha'].includes(value)
  },
  form: {
    type: Object,
    required: true
  },
  language: {
    type: String,
    required: true
  },
  darkMode: {
    type: Boolean,
    default: false
  }
})

const config = useRuntimeConfig()
const recaptchaSiteKey = config.public.recaptchaSiteKey
const hCaptchaSiteKey = config.public.hCaptchaSiteKey

const captchaRef = ref(null)
const isIframe = ref(false)
const showCaptcha = ref(true)
const componentKey = ref(0)

const formFieldName = computed(() => props.provider === 'recaptcha' ? 'g-recaptcha-response' : 'h-captcha-response')

const isSiteKeyAvailable = computed(() => {
  if (props.provider === 'recaptcha') {
    return !!recaptchaSiteKey
  } else if (props.provider === 'hcaptcha') {
    return !!hCaptchaSiteKey
  }
  return false
})

// Watch for provider changes to reset the form field
watch(() => props.provider, async (newProvider, oldProvider) => {
  if (newProvider !== oldProvider) {
    // Clear old provider's value
    if (oldProvider === 'recaptcha') {
      props.form['g-recaptcha-response'] = null
    } else if (oldProvider === 'hcaptcha') {
      props.form['h-captcha-response'] = null
    }

    // Force remount by toggling visibility and incrementing key
    showCaptcha.value = false
    
    // Wait longer to ensure complete cleanup
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    componentKey.value++
    await nextTick()
    
    // Wait again before showing new captcha
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    showCaptcha.value = true
  }
})

onMounted(() => {
  isIframe.value = window.self !== window.top
})

// Add a ref to track if captcha was completed
const wasCaptchaCompleted = ref(false)

// Handle captcha verification
const onCaptchaVerify = (token) => {
  wasCaptchaCompleted.value = true
  props.form[formFieldName.value] = token
  // Also set the DOM element value for compatibility with existing code
  if (import.meta.client) {
    const element = document.getElementsByName(formFieldName.value)[0]
    if (element) element.value = token
  }
}

// Handle captcha expiration
const onCaptchaExpired = () => {
  wasCaptchaCompleted.value = false
  props.form[formFieldName.value] = null
  // Also clear the DOM element value for compatibility with existing code
  if (import.meta.client) {
    const element = document.getElementsByName(formFieldName.value)[0]
    if (element) element.value = ''
  }
}

// Handle iframe resizing
const resizeIframe = (height) => {
  if (!isIframe.value) return
  
  try {
    window.parentIFrame?.size(height)
  } catch {
    // Silently handle error
  }
}

// Handle captcha open/close for iframe resizing
const onCaptchaOpen = () => {
  resizeIframe(500)
  // Ensure the captcha is visible by scrolling to it
  if (import.meta.client) {
    nextTick(() => {
      const captchaElement = captchaRef.value?.$el
      if (captchaElement) {
        captchaElement.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    })
  }
}

const onCaptchaClose = () => {
  resizeIframe(0)
}

// Method to reset captcha - can be called from parent
defineExpose({
  reset: () => {
    // Only do a full reset if the captcha was previously completed
    if (captchaRef.value) {
      if (wasCaptchaCompleted.value) {
        wasCaptchaCompleted.value = false
        captchaRef.value.reset()
      }
    }
  }
})
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style> 