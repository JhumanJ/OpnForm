<template>
  <div
    id="widget_login"
    class="tgme_widget_login medium nouserpic"
  >
    <button
      class="btn tgme_widget_login_button"
      @click.prevent="handleAuth"
    >
      <i class="tgme_widget_login_button_icon" />Log in with Telegram
    </button>
  </div>
</template>

<script setup>
defineProps({
  service: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['auth-data'])

const botId = computed(() => useFeatureFlag('services.telegram.bot_id'))

const loadTelegramWidget = () => {
  if (!botId.value) return

  const script = document.createElement('script')
  script.async = true
  script.src = 'https://telegram.org/js/telegram-widget.js'
  document.head.appendChild(script)
}

const handleAuth = () => {
  if (window.Telegram.Login) {
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

<style>
.tgme_widget_login {
  margin: 0;
  padding: 0;
}
.tgme_widget_login_button {
  display: inline-block;
  padding: 8px 16px;
  background-color: #54a9eb;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  line-height: 20px;
  text-decoration: none;
}
.tgme_widget_login_button:hover {
  background-color: #4a96d1;
}
.tgme_widget_login_button_icon {
  display: inline-block;
  vertical-align: middle;
  margin-right: 8px;
  width: 20px;
  height: 20px;
  background: url('data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMjQgMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTkuNzggMTguNjVsLjI4LTQuMjMgNy42OC02Ljk0Yy4zNC0uMy0uMDctLjQ2LS41Mi0uMTlMMy42NyAxMS4xN2wtMy45NC0xLjJjLS44NS0uMjQtLjg1LS44Mi4xOS0xLjI1bDE1LjM3LTUuOTFjLjcxLS4yNyAxLjM5LjE5IDEuMTIgMS4zN2wtMi42MSAxMi4yOGMtLjE5Ljg5LS43MyAxLjExLTEuNDguNjlsLTQuMDctMy0yLjQ3IDIuNGMtLjI4LjI4LS41Mi41Mi0uOTQuNTJ6IiBmaWxsPSIjZmZmIi8+PC9zdmc+') 0 0 no-repeat;
  background-size: contain;
}
</style>