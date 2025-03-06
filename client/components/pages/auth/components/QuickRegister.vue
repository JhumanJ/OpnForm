<template>
  <div>
    <!--  Login modal  -->
    <modal
      :show="appStore.quickLoginModal"
      max-width="lg"
      @close="appStore.quickLoginModal=false"
    >
      <template #icon>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-8 h-8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </template>
      <template #title>
        Login to OpnForm
      </template>
      <div class="px-4">
        <login-form
          :is-quick="true"
          @open-register="openRegister"
          @after-quick-login="afterQuickLogin"
        />
      </div>
    </modal>

    <!--  Register modal  -->
    <modal
      :show="appStore.quickRegisterModal"
      max-width="lg"
      @close="appStore.quickRegisterModal=false"
    >
      <template #icon>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-8 h-8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </template>
      <template #title>
        Create an account
      </template>
      <div class="px-4">
        <register-form
          :is-quick="true"
          @open-login="openLogin"
          @after-quick-login="afterQuickLogin"
        />
      </div>
    </modal>
  </div>
</template>

<script setup>
import LoginForm from "./LoginForm.vue"
import RegisterForm from "./RegisterForm.vue"

const appStore = useAppStore()
const emit = defineEmits(['afterLogin', 'close', 'reopen'])

onMounted(() => {
  document.addEventListener('quick-login-complete', () => {
    afterQuickLogin()
  })
})

onUnmounted(() => {
  document.removeEventListener('quick-login-complete', () => {
    afterQuickLogin()
  })
})

const openLogin = () => {
  appStore.quickLoginModal = true
  appStore.quickRegisterModal = false
  emit('close')
}

const openRegister = () => {
  appStore.quickLoginModal = false
  appStore.quickRegisterModal = true
  emit('reopen')
}

const afterQuickLogin = () => {
  setTimeout(() => {
    appStore.quickLoginModal = false
    emit('afterLogin')
  }, 1000)
}
</script>
