<template>
  <div>
    <!--  Login modal  -->
    <modal
      compact-header
      :show="appStore.quickLoginModal"
      max-width="lg"
      :closeable="!appStore.isUnauthorizedError"
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
        <template v-if="appStore.isUnauthorizedError">
          <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-md">
            <p class="text-amber-800 dark:text-amber-200 text-sm font-medium">
              Your session has expired. Please log in again to continue.
            </p>
          </div>
        </template>
        <login-form
          :is-quick="true"
          @open-register="openRegister"
        />

        <template v-if="appStore.isUnauthorizedError">
          <div class="flex items-center my-6">
            <div class="h-[1px] bg-gray-300 dark:bg-gray-600 flex-1" />
            <div class="px-4 text-gray-500 text-sm">
              or
            </div>
            <div class="h-[1px] bg-gray-300 dark:bg-gray-600 flex-1" />
          </div>
          <UButton
            icon="i-heroicons-arrow-right-on-rectangle"
            type="button"
            variant="solid"
            color="white"
            label="Logout"
            :block="true"
            size="lg"
            @click="logout"
          />
          <p class="text-gray-500 text-sm text-center mt-2">
            Progress will be lost.
          </p>
        </template>
      </div>
    </modal>

    <!--  Register modal  -->
    <modal
      compact-header
      :show="appStore.quickRegisterModal"
      max-width="lg"
      :closeable="!appStore.isUnauthorizedError"
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
        />
      </div>
    </modal>
  </div>
</template>

<script setup>
import LoginForm from "./LoginForm.vue"
import RegisterForm from "./RegisterForm.vue"
import { WindowMessageTypes } from "~/composables/useWindowMessage"

const appStore = useAppStore()

// Define emits for component interactions
const emit = defineEmits(['close', 'reopen'])

const windowMessage = useWindowMessage(WindowMessageTypes.LOGIN_COMPLETE)

// Set up a listener for after-login messages
const afterLoginMessage = useWindowMessage(WindowMessageTypes.AFTER_LOGIN)

onMounted(() => {
  // Listen for login-complete messages from popup window
  windowMessage.listen(() => {
    // Handle social login completion
    handleSocialLogin()
  })
  
  // Set up after-login listener for component communication
  afterLoginMessage.listen(() => {
    afterQuickLogin()
  })
  
  // Reset the unauthorized error flag when component is unmounted
  onUnmounted(() => {
    appStore.isUnauthorizedError = false
  })
})

// Handle social login completion
const handleSocialLogin = () => {
  // This function is only called for social logins, so we refresh tokens
  const authStore = useAuthStore()
  const tokenValue = useCookie("token").value
  const adminTokenValue = useCookie("admin_token").value
  
  // Re-initialize the store with the latest tokens from cookies
  authStore.initStore(tokenValue, adminTokenValue)
  
  // Then proceed with normal login flow
  afterQuickLogin()
}

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

const afterQuickLogin = async () => {
  // Reset unauthorized error flag immediately
  appStore.isUnauthorizedError = false
  
  // Verify authentication is complete using the useAuth composable
  const auth = useAuth()
  await auth.verifyAuthentication()
  
  // Close both login and register modals
  appStore.quickLoginModal = false
  appStore.quickRegisterModal = false
  
  // Show success alert
  useAlert().success("Successfully logged in. Welcome back!")
  
  // Use the window message for after-login instead of emitting the event
  afterLoginMessage.send(window, { useMessageChannel: false })
}

const logout = async () => {
  appStore.isUnauthorizedError = false
  appStore.quickLoginModal = false
  appStore.quickRegisterModal = false
  useRouter().push('/login')
}
</script>
