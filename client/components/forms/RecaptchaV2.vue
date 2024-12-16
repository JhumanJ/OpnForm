<template>
  <div>
    <div
      ref="recaptchaContainer"
      class="g-recaptcha"
      :data-sitekey="sitekey"
      :data-theme="theme"
    />
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
  }
})

const emit = defineEmits(['verify', 'expired'])
const recaptchaContainer = ref(null)

onMounted(async () => {
  if (!document.querySelector('script[src*="recaptcha/api.js"]')) {
    const script = document.createElement('script')
    script.src = 'https://www.google.com/recaptcha/api.js'
    script.async = true
    script.defer = true
    document.head.appendChild(script)

    await new Promise((resolve) => {
      script.onload = resolve
    })
  }

  // Wait for grecaptcha to be available
  await new Promise((resolve) => {
    const checkGrecaptcha = () => {
      if (window.grecaptcha?.ready) {
        resolve()
      } else {
        setTimeout(checkGrecaptcha, 100)
      }
    }
    checkGrecaptcha()
  })

  window.recaptchaCallback = (token) => {
    emit('verify', token)
  }

  window.recaptchaExpiredCallback = () => {
    emit('expired')
  }

  try {
    window.grecaptcha.render(recaptchaContainer.value, {
      sitekey: props.sitekey,
      theme: props.theme,
      callback: 'recaptchaCallback',
      'expired-callback': 'recaptchaExpiredCallback'
    })
  } catch (error) {
    console.error('Error rendering reCAPTCHA:', error)
  }
})

onBeforeUnmount(() => {
  delete window.recaptchaCallback
  delete window.recaptchaExpiredCallback
})
</script>