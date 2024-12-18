<template>
  <div>
    <form
      @submit.prevent="register"
      @keydown="form.onKeydown($event)"
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
        v-if="recaptchaSiteKey"
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
        class="mb-3"
        :required="true"
      >
        <template #label>
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
        </template>
      </checkbox-input>

      <!-- Submit Button -->
      <v-button
        class="w-full mt-4"
        :loading="form.busy"
      >
        Create account
      </v-button>

      <template v-if="useFeatureFlag('services.google.auth')">
        <p class="text-gray-600/50 text-sm text-center my-4">
          Or
        </p>
        <v-button
          native-type="buttom"
          color="white"
          class="space-x-4 flex items-center w-full"
          :loading="false"
          @click.prevent="signInwithGoogle"
        >
          <Icon name="devicon:google" />
          <span class="mx-2">Sign in with Google</span>
        </v-button>
      </template>

      <p class="text-gray-500 mt-4 text-sm text-center">
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
    </form>
  </div>
</template>

<script>
import {opnFetch} from "~/composables/useOpnApi.js"
import { fetchAllWorkspaces } from "~/stores/workspaces.js"

export default {
  name: "RegisterForm",
  components: {},
  props: {
    isQuick: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  emits: ['afterQuickLogin', 'openLogin'],

  setup() {
    const { $utm } = useNuxtApp()
    return {
      authStore: useAuthStore(),
      formsStore: useFormsStore(),
      workspaceStore: useWorkspacesStore(),
      providersStore: useOAuthProvidersStore(),
      runtimeConfig: useRuntimeConfig(),
      logEvent: useAmplitude().logEvent,
      $utm
    }
  },

  data: () => ({
    form: useForm({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      agree_terms: false,
      appsumo_license: null,
      utm_data: null,
      'g-recaptcha-response': null
    }),
    disableEmail: false,
  }),

  computed: {
    recaptchaSiteKey() {
      return this.runtimeConfig.public.recaptchaSiteKey
    },
    hearAboutUsOptions() {
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
    },
  },

  mounted() {
    // Set appsumo license
    if (
      this.$route.query.appsumo_license !== undefined &&
      this.$route.query.appsumo_license
    ) {
      this.form.appsumo_license = this.$route.query.appsumo_license
    }

    if (this.$route.query?.invite_token) {
      if (this.$route.query?.email) {
        this.form.email = this.$route.query?.email
        this.disableEmail = true
      }
      this.form.invite_token = this.$route.query?.invite_token
    }
  },

  methods: {
    async register() {
      let data
      this.form.utm_data = this.$utm.value
      // Reset captcha after submission
      if (import.meta.client && this.recaptchaSiteKey) {
        this.$refs.captcha.reset()
      }
      try {
        // Register the user.
        data = await this.form.post("/register")
      } catch (err) {
        useAlert().error(err.response?._data?.message)
        return false
      }

      // Log in the user.
      const tokenData = await this.form.post("/login")

      // Save the token.
      this.authStore.setToken(tokenData.token)

      const userData = await opnFetch("user")
      this.authStore.setUser(userData)

      const workspaces = await fetchAllWorkspaces()
      this.workspaceStore.set(workspaces.data.value)

      // Load forms
      this.formsStore.loadAll(this.workspaceStore.currentId)

      this.logEvent("register", {source: this.form.hear_about_us})
      try {
        useGtm().trackEvent({
          event: 'register',
          source: this.form.hear_about_us
        })
      } catch (error) {
        console.error(error)
      }

      // AppSumo License
      if (data.appsumo_license === false) {
        useAlert().error(
          "Invalid AppSumo license. This probably happened because this license was already" +
          " attached to another OpnForm account. Please contact support.",
        )
      } else if (data.appsumo_license === true) {
        useAlert().success(
          "Your AppSumo license was successfully activated! You now have access to all the" +
          " features of the AppSumo deal.",
        )
      }

      // Redirect
      if (this.isQuick) {
        this.$emit("afterQuickLogin")
      } else {
        // If is invite just redirect to home
        if (this.form.invite_token) {
          useAlert().success("You have successfully accepted the invite and joined this workspace.")
          this.$router.push({name: "home"})
        } else {
          this.$router.push({name: "forms-create"})
        }
      }
    },
    signInwithGoogle() {
      this.providersStore.guestConnect('google', true)
    }
  },
}
</script>
