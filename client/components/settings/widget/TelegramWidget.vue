<template>
  <div id="telegram-login-container" />
</template>

<script setup>
const props = defineProps({
  service: {
    type: Object,
    required: true
  }
})

const loadTelegramWidget = () => {
  if (!useFeatureFlag('services.telegram.bot')) return
  
  const script = document.createElement('script')
  script.async = true
  script.src = 'https://telegram.org/js/telegram-widget.js?22'
  script.setAttribute('data-telegram-login', useFeatureFlag('services.telegram.bot'))
  script.setAttribute('data-size', 'large')
  script.setAttribute('data-auth-url', useFeatureFlag('services.telegram.redirect'))
  script.setAttribute('data-request-access', 'write')
  
  document.getElementById('telegram-login-container').appendChild(script)
}

onMounted(() => {
  loadTelegramWidget()
})
</script>