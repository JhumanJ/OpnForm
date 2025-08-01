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
          :form="form"
          class="w-full md:w-1/2"
          name="remember"
          size="small"
          label="Remember me"
        />

        <div class="w-full md:w-1/2 text-right">
          <a
            href="#"
            class="text-xs hover:underline text-neutral-500 sm:text-sm hover:text-neutral-700"
            @click.prevent="showForgotModal = true"
          >
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <UButton
        class="mt-2"
        block
        size="lg"
        :loading="form.busy"
        type="submit"
        label="Log in to continue"
      />

      <UButton
        v-if="useFeatureFlag('services.google.auth') && !useFeatureFlag('self_hosted')"
        native-type="button"
        color="neutral"
        variant="outline"
        size="lg"
        class="space-x-4 mt-4 flex items-center"
        block
        :disabled="form.busy"
        :loading="false"
        @click.prevent="signInwithGoogle"
        icon="devicon:google"
        label="Sign in with Google"
      />
      <p
        v-if="!useFeatureFlag('self_hosted')"
        class="text-neutral-500 text-sm text-center mt-4"
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

    <!-- Google One Tap -->
    <ClientOnly>
      <GoogleOneTap context="signin" />
    </ClientOnly>
  </div>
</template>

<script setup>
import ForgotPasswordModal from "../ForgotPasswordModal.vue"
import GoogleOneTap from "~/components/vendor/GoogleOneTap.vue"
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
const router = useRouter()
const { login: loginMutationFactory } = useAuth()

// Reactive data
const form = useForm({
  email: "",
  password: "",
  remember: false,
})

const loginMutation = loginMutationFactory()
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
const login = () => {
  form.mutate(loginMutation).then(() => {
    redirect()
  }).catch((error) => {
    console.log(error)
    if (error.response?._data?.message == "You must change your credentials when in self host mode") {
      redirect()
    }
  })
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

const showOAuthError = (error) => {
  if (error.response?.status === 422 && error.response?.data?.message) {
    useAlert().error(error.response.data.message)
  } else {
    useAlert().error("Sign-in failed. Please try again.")
  }
}

const signInwithGoogle = () => {
  try {
    oAuth.guestConnect('google', true)
  } catch (error) {
    showOAuthError(error)
  }
}
</script>
