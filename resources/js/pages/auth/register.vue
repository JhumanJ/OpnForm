<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:max-w-6xl mx-auto px-4 flex md:flex-row-reverse flex-wrap">
        <div class="w-full md:w-1/2 md:p-6">
          <div class="border rounded-md p-6 shadow-md sticky top-4">
            <h2 class="font-semibold text-2xl">
              Create an account
            </h2>
            <small>Sign up in less than 2 minutes.</small>

            <form @submit.prevent="register" @keydown="form.onKeydown($event)" class="mt-4">
              <!-- Name -->
              <text-input name="name" :form="form" :label="$t('name')" placeholder="Your name" :required="true" />

              <!-- Email -->
              <text-input name="email" :form="form" :label="$t('email')" :required="true" placeholder="Your email address" />

              <select-input name="hear_about_us" :options="hearAboutUsOptions" :form="form" placeholder="Select option"
                            label="How did you hear about us?" :required="true"
              />

              <!-- Password -->
              <text-input native-type="password" placeholder="Enter password"
                          name="password" :form="form" :label="$t('password')" :required="true"
              />

              <!-- Password Confirmation-->
              <text-input native-type="password" :form="form" :required="true" placeholder="Enter confirm password"
                          name="password_confirmation"  :label="$t('confirm_password')"
              />

              <checkbox-input :form="form" name="agree_terms" :required="true" 
                      label="I agree with the Terms and conditions and Privacy policy of the website and I accept them."
              />

              <!-- Submit Button -->
              <v-button :loading="form.busy">Create an account</v-button>

              <p class="text-gray-500 mt-4">
                Already have an account?  <router-link :to="{name:'login'}" class="font-semibold ml-1">Log In</router-link>
              </p>

              <!-- GitHub Register Button -->
              <login-with-github />
            </form>
          </div>
        </div>
        <div class="w-full md:w-1/2 md:p-6 mt-8 md:mt-0 ">
          <h1 class="font-bold">
            Create beautiful Notion forms and share them anywhere
          </h1>
          <p class="text-gray-900 my-4 text-lg">
            It takes seconds, you don't need to know how to code and it's free.
          </p>
          <div class="flex flex-wrap justify-center">
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Unlimited forms
            </p>
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Unlimited fields
            </p>
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Unlimited submissions
            </p>
          </div>
          <div class="mt-3 p-6">
            <testimonials />
          </div>
        </div>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub'
import SelectInput from '../../components/forms/SelectInput'
import OpenFormFooter from '../../components/pages/OpenFormFooter'
import { initCrisp } from '../../middleware/check-auth'
import Testimonials from '../../components/pages/welcome/Testimonials'

export default {
  components: {
    Testimonials,
    SelectInput,
    LoginWithGithub,
    OpenFormFooter
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      agree_terms: false
    }),
    mustVerifyEmail: false
  }),

  computed: {
    hearAboutUsOptions () {
      const options = [
        { name: 'Facebook', value: 'facebook' },
        { name: 'Twitter', value: 'twitter' },
        { name: 'Reddit', value: 'reddit' },
        { name: 'Github', value: 'github' },
        { name: 'Search Engine (Google, DuckDuckGo...)', value: 'search_engine' },
        { name: 'Friend or Colleague', value: 'friend_colleague' },
        { name: 'Blog/Article', value: 'blog_article' }
      ].map((value) => ({ value, sort: Math.random() }))
        .sort((a, b) => a.sort - b.sort)
        .map(({ value }) => value)
      options.push({ name: 'Other', value: 'other' })
      return options
    }
  },

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/api/register')

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/api/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Track event
        this.$logEvent('register', { source: this.form.hear_about_us })
        initCrisp(data).then(() => {
          this.$getCrisp().push(['set', 'session:event', [[['register', {}, 'blue']]]])
        })

        // Redirect home.
        this.$router.push({ name: 'forms.create' })
      }
    }
  }
}
</script>
