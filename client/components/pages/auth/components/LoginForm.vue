<template>
  <div>
    <forgot-password-modal
      :show="showForgotModal"
      @close="showForgotModal = false"
    />

    <form
      method="POST"
      class="mt-4"
      @submit.prevent="login"
      @keydown="form.onKeydown($event)"
    >
      <!-- Email -->
      <text-input
        name="email"
        :form="form"
        label="Email"
        :required="true"
        placeholder="Your email address"
      />

      <!-- Password -->
      <text-input
        native-type="password"
        placeholder="Your password"
        name="password"
        :form="form"
        label="Password"
        :required="true"
      />

      <!-- Remember Me -->
      <div class="relative flex items-center mt-3">
        <CheckboxInput
          v-model="remember"
          class="w-full md:w-1/2"
          name="remember"
          size="small"
          label="Remember me"
        />

        <div class="w-full md:w-1/2 text-right">
          <a
            href="#"
            class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700"
            @click.prevent="showForgotModal = true"
          >
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <v-button
        class="w-full flex mt-2"
        :loading="form.busy || loading"
      >
        Log in to continue
      </v-button>

      <v-button
        v-if="useFeatureFlag('services.google.auth')"
        native-type="button"
        color="white"
        class="space-x-4 mt-4 flex items-center w-full"
        :loading="false"
        @click.prevent="signInwithGoogle"
      >
        <Icon
          name="devicon:google"
          class="w-4 h-4"
        />
        <span class="mx-2">Sign in with Google</span>
      </v-button>
      <p
        v-if="!useFeatureFlag('self_hosted')"
        class="text-gray-500 text-sm text-center mt-4"
      >
        Don't have an account?
        <a
          v-if="isQuick"
          href="#"
          class="font-semibold ml-1"
          @click.prevent="$emit('openRegister')"
        >Sign Up</a>
        <NuxtLink
          v-else
          :to="{ name: 'register' }"
          class="font-semibold ml-1"
        >
          Sign Up
        </NuxtLink>
      </p>
    </form>
  </div>
</template>

<script setup>
import ForgotPasswordModal from "../ForgotPasswordModal.vue"
import { WindowMessageTypes } from "~/composables/useWindowMessage"

// Props
const props = defineProps({
  isQuick: {
    type: Boolean,
    required: false,
    default: false,
  },
})

// Emits
defineEmits(['openRegister'])

// Composables
const oAuth = useOAuth()
const authFlow = useAuthFlow()
const router = useRouter()

// Reactive data
const form = useForm({
  email: "",
  password: "",
})

const loading = ref(false)
const remember = ref(false)
const showForgotModal = ref(false)

// Lifecycle
onMounted(() => {
  // Use the window message composable
  const windowMessage = useWindowMessage(WindowMessageTypes.LOGIN_COMPLETE)
  
  // Listen for login complete messages
  windowMessage.listen(() => {
    redirect()
  })
})

// Methods
const login = async () => {
  loading.value = true
  
  try {
    await authFlow.loginWithCredentials(form, remember.value)
    redirect()
  } catch (error) {
    console.log(error)
    if (error.response?._data?.message == "You must change your credentials when in self host mode") {
      redirect()
    }
  } finally {
    loading.value = false
  }
}

const redirect = () => {
  if (props.isQuick) {
    // Use window message instead of event
    const afterLoginMessage = useWindowMessage(WindowMessageTypes.AFTER_LOGIN)
    afterLoginMessage.send(window)
    return
  }

  const intendedUrlCookie = useCookie("intended_url")

  if (intendedUrlCookie.value) {
    router.push({ path: intendedUrlCookie.value })
    useCookie("intended_url").value = null
  } else {
    router.push({ name: "home" })
  }
}

const signInwithGoogle = () => {
  oAuth.guestConnect('google', true)
}
</script>
