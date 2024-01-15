<template>
  <div>
    <form class="mt-4" @submit.prevent="register" @keydown="form.onKeydown($event)">
      <!-- Name -->
      <text-input name="name" :form="form" label="Name" placeholder="Your name" :required="true"/>

      <!-- Email -->
      <text-input name="email" :form="form" label="Email" :required="true" placeholder="Your email address"/>

      <select-input name="hear_about_us" :options="hearAboutUsOptions" :form="form" placeholder="Select option"
                    label="How did you hear about us?" :required="true"
      />

      <!-- Password -->
      <text-input native-type="password" placeholder="Enter password"
                  name="password" :form="form" label="Password" :required="true"
      />

      <!-- Password Confirmation-->
      <text-input native-type="password" :form="form" :required="true" placeholder="Enter confirm password"
                  name="password_confirmation" label="Confirm Password"
      />

      <checkbox-input :form="form" name="agree_terms" :required="true">
        <template #label>
          I agree with the
          <NuxtLink :to="{name:'terms-conditions'}" target="_blank">
            Terms and conditions
          </NuxtLink>
          and
          <NuxtLink :to="{name:'privacy-policy'}" target="_blank">
            Privacy policy
          </NuxtLink>
          of the website and I accept them.
        </template>
      </checkbox-input>

      <!-- Submit Button -->
      <v-button :loading="form.busy">
        Create an account
      </v-button>

      <p class="text-gray-500 mt-4">
        Already have an account?
        <a v-if="isQuick" href="#" class="font-semibold ml-1" @click.prevent="$emit('openLogin')">Log In</a>
        <NuxtLink v-else :to="{name:'login'}" class="font-semibold ml-1">
          Log In
        </NuxtLink>
      </p>
    </form>
  </div>
</template>

<script>
import {opnFetch} from "~/composables/useOpnApi.js";
import {fetchAllWorkspaces} from "~/stores/workspaces.js";

export default {
  name: 'RegisterForm',
  components: {},
  props: {
    isQuick: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  setup() {
    return {
      authStore: useAuthStore(),
      formsStore: useFormsStore(),
      workspaceStore: useWorkspacesStore(),
      logEvent: useAmplitude().logEvent
    }
  },

  data: () => ({
    form: useForm({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      agree_terms: false,
      appsumo_license: null
    }),
  }),

  computed: {
    hearAboutUsOptions() {
      const options = [
        {name: 'Facebook', value: 'facebook'},
        {name: 'Twitter', value: 'twitter'},
        {name: 'Reddit', value: 'reddit'},
        {name: 'Github', value: 'github'},
        {name: 'Search Engine (Google, DuckDuckGo...)', value: 'search_engine'},
        {name: 'Friend or Colleague', value: 'friend_colleague'},
        {name: 'Blog/Article', value: 'blog_article'}
      ].map((value) => ({value, sort: Math.random()}))
        .sort((a, b) => a.sort - b.sort)
        .map(({value}) => value)
      options.push({name: 'Other', value: 'other'})
      return options
    }
  },

  mounted() {
    // Set appsumo license
    if (this.$route.query.appsumo_license !== undefined && this.$route.query.appsumo_license) {
      this.form.appsumo_license = this.$route.query.appsumo_license
    }
  },

  methods: {
    async register() {
      // Register the user.
      const data = await this.form.post('/register')

      // Log in the user.
      const tokenData = await this.form.post('/login')

      // Save the token.
      this.authStore.setToken(tokenData.token)

      const userData = await opnFetch('user')
      this.authStore.setUser(userData)

      const workspaces = await fetchAllWorkspaces()
      this.workspaceStore.set(workspaces.data.value)

      // Load forms
      this.formsStore.loadAll(this.workspaceStore.currentId)

      this.logEvent('register', {source: this.form.hear_about_us})

      // AppSumo License
      if (data.appsumo_license === false) {
        useAlert().error('Invalid AppSumo license. This probably happened because this license was already' +
          ' attached to another OpnForm account. Please contact support.')
      } else if (data.appsumo_license === true) {
        useAlert().success('Your AppSumo license was successfully activated! You now have access to all the' +
          ' features of the AppSumo deal.')
      }

      // Redirect
      if (this.isQuick) {
        this.$emit('afterQuickLogin')
      } else {
        this.$router.push({name: 'forms-create'})
      }
    }
  }
}
</script>
