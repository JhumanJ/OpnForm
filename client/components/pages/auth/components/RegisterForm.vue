<template>
  <VForm size="sm"
    method="POST"
    @submit.prevent="register"
    class="flex flex-col gap-1"
  >
    <!-- Name -->
    <text-input
      name="name"
      :form="form"
      label="Name"
      placeholder="Your name"
      :required="true"
    />

    <!-- Email -->
    <text-input
      name="email"
      :form="form"
      label="Email"
      :required="true"
      :disabled="disableEmail"
      placeholder="Your email address"
    />

    <select-input
      v-if="!disableEmail"
      name="hear_about_us"
      :options="hearAboutUsOptions"
      :form="form"
      placeholder="Select option"
      label="How did you hear about us?"
      :required="true"
    />

    <!-- Password -->
    <text-input
      native-type="password"
      placeholder="Enter password"
      name="password"
      :form="form"
      label="Password"
      :required="true"
    />

    <!-- Password Confirmation-->
    <text-input
      native-type="password"
      :form="form"
      :required="true"
      placeholder="Enter confirm password"
      name="password_confirmation"
      label="Confirm Password"
    />

    <!-- Captcha -->
    <div
      v-if="reCaptchaSiteKey"
      class="my-4 px-2 mx-auto w-max"
    >
      <CaptchaInput
        ref="captcha"
        provider="recaptcha"
        :form="form"
        language="en"
      />
    </div>

    <checkbox-input
      :form="form"
      name="agree_terms"
      class="my-3"
      :required="true"
    >
      <template #label>
        <label for="agree_terms">
          I agree with the
          <NuxtLink
            :to="{ name: 'terms-conditions' }"
            target="_blank"
            class="underline"
          >
            Terms and conditions
          </NuxtLink>
          and
          <NuxtLink
            :to="{ name: 'privacy-policy' }"
            target="_blank"
            class="underline"
          >
            Privacy policy
          </NuxtLink>
          of the website and I accept them.
        </label>
      </template>
    </checkbox-input>

    <!-- Submit Button -->
    <UButton
      class="mt-4"
      block
      size="lg"
      :loading="form.busy"
      type="submit"
      label="Create account"
    />

    <template v-if="useFeatureFlag('services.google.auth') && !useFeatureFlag('self_hosted')">
      <p class="text-neutral-500 text-sm text-center my-4">
        OR
      </p>
      <UButton
        color="neutral"
        variant="outline"
        size="lg"
        class="space-x-4 flex items-center"
        block
        :loading="false"
        @click.prevent="signInwithGoogle"
        icon="devicon:google"
        label="Sign in with Google"
      />
    </template>

    <p class="text-neutral-500 mt-4 text-sm text-center">
      Already have an account?
      <a
        v-if="isQuick"
        href="#"
        class="font-medium ml-1"
        @click.prevent="$emit('openLogin')"
      >Log In</a>
      <NuxtLink
        v-else
        :to="{ name: 'login' }"
        class="font-semibold ml-1"
      >
        Log In
      </NuxtLink>
    </p>
  </VForm>
</template>

<script setup>
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
defineEmits(['openLogin'])

// Composables
const { $utm } = useNuxtApp()
const oAuth = useOAuth()
const runtimeConfig = useRuntimeConfig()
const { register: registerMutationFactory } = useAuth()
const router = useRouter()
const route = useRoute()

const registerMutation = registerMutationFactory()

// Reactive data
const form = useForm({
  name: "",
  email: "",
  hear_about_us: "",
  password: "",
  password_confirmation: "",
  agree_terms: false,
  appsumo_license: null,
  utm_data: null,
  'g-recaptcha-response': null
})

const disableEmail = ref(false)
const captcha = ref(null)

// Computed
const reCaptchaSiteKey = computed(() => {
  return runtimeConfig.public.reCaptchaSiteKey
})

const hearAboutUsOptions = computed(() => {
  const options = [
    {name: "Facebook", value: "facebook"},
    {name: "Twitter", value: "twitter"},
    {name: "Reddit", value: "reddit"},
    {name: "Github", value: "github"},
    {
      name: "Search Engine (Google, DuckDuckGo...)",
      value: "search_engine",
    },
    {name: "Friend or Colleague", value: "friend_colleague"},
    {name: "Blog/Article", value: "blog_article"},
  ]
    .map((value) => ({value, sort: Math.random()}))
    .sort((a, b) => a.sort - b.sort)
    .map(({value}) => value)
  options.push({name: "Other", value: "other"})
  return options
})

// Lifecycle
onMounted(() => {
  // Use the window message composable
  const windowMessage = useWindowMessage(WindowMessageTypes.LOGIN_COMPLETE)
  
  // Listen for login complete messages
  windowMessage.listen(() => {
    redirect()
  })

  // Set appsumo license
  if (
    route.query.appsumo_license !== undefined &&
    route.query.appsumo_license
  ) {
    form.appsumo_license = route.query.appsumo_license
  }

  if (route.query?.invite_token) {
    if (route.query?.email) {
      form.email = route.query?.email
      form.hear_about_us = 'invite'
      disableEmail.value = true
    }
    form.invite_token = route.query?.invite_token
  }
})

// Methods
const register = () => {
  form.utm_data = {...$utm.value}
  $utm.value = null
  
  form.mutate(registerMutation, {
    data: {
      formData: form.data(),
      source: form.hear_about_us
    }
  }).then(() => {
    useAlert().success({
      title: "Welcome to OpnForm 👋",
      ...!props.isQuick ? {description: "Time to create your first form!"} : {}
    })
    redirect()
  }).catch((err) => {
    useAlert().error(err.response?._data?.message)
  }).finally(() => {
    // Reset captcha after submission
    if (import.meta.client && reCaptchaSiteKey.value) {
      captcha.value.reset()
    }
  })
}

const redirect = () => {
  if (props.isQuick) {
    // Use window message instead of event
    const afterLoginMessage = useWindowMessage(WindowMessageTypes.AFTER_LOGIN)
    afterLoginMessage.send(window)
  } else {
    // If is invite just redirect to home
    if (form.invite_token) {
      useAlert().success("You have successfully accepted the invite and joined this workspace.")
      router.push({name: "home"})
    } else {
      router.push({name: "forms-create"})
    }
  }
}

const signInwithGoogle = () => {
  oAuth.guestConnect('google', true)
}
</script>
