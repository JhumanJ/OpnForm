<template>
  <div
    id="widget_login"
    class="flex flex-col gap-4"
  >
    <p class="text-sm text-gray-500">
      <a href="https://telegram.org" target="_blank" class="text-primary-500 hover:underline">Telegram</a> is a secure messaging app that works across all devices and platforms. 
      Connect your account to receive instant notifications whenever someone submits this form!
    </p>
    <div class="flex justify-center">
      <UButton
        :disabled="!botId"
        icon="i-mdi-telegram"
        @click.prevent="handleAuth"
    >
        Log in with Telegram
      </UButton>
    </div>
  </div>
</template>

<script setup>
import { useFeatureFlagsStore } from '~/stores/featureFlags'

defineProps({
  service: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['auth-data'])

const featureFlagsStore = useFeatureFlagsStore()
const botId = computed(() => featureFlagsStore.getFlag('services.telegram.bot_id'))

const loadTelegramWidget = () => {
  if (!botId.value) return

  const script = document.createElement('script')
  script.async = true
  script.src = 'https://telegram.org/js/telegram-widget.js'
  document.head.appendChild(script)
}

const handleAuth = () => {
  console.log(parseInt(botId.value))
  if (window.Telegram?.Login) {
    window.Telegram.Login.auth(
      { bot_id: botId.value, request_access: true },
      (data) => {
        emit('auth-data', data)
      }
    )
  } else {
    useAlert().error('Telegram login is not available')
  }
}

onMounted(() => {
  loadTelegramWidget()
})
</script>

