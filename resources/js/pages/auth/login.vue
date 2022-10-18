<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:max-w-6xl mx-auto px-4 flex md:flex-row-reverse flex-wrap">
        <div class="w-full md:w-1/2 md:p-6">
          <div class="border rounded-md p-6 shadow-md sticky top-4">
            <h2 class="font-semibold text-2xl">
              Login to OpnForm
            </h2>
            <small>Welcome back! Please enter your details.</small>

            <form @submit.prevent="login" @keydown="form.onKeydown($event)" class="mt-4">
              <!-- Email -->
              <text-input name="email" :form="form" :label="$t('email')" :required="true" placeholder="Your email address" />

              <!-- Password -->
              <text-input native-type="password" placeholder="Your password"
                          name="password" :form="form" :label="$t('password')" :required="true"
              />

              <!-- Remember Me -->
              <div class="relative flex items-center my-5">
                <v-checkbox v-model="remember" class="w-full md:w-1/2" name="remember">
                  {{ $t('remember_me') }}
                </v-checkbox>

                <div class="w-full md:w-1/2 text-right">
                  <router-link :to="{ name: 'password.request' }" class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700">
                    Forgot your password?
                  </router-link>
                </div>
              </div>

              <!-- Submit Button -->
              <v-button dusk="btn_login" :loading="form.busy">Log in to continue</v-button>

              <p class="text-gray-500 mt-4">
                Don't have an account?  <router-link :to="{name:'register'}" class="font-semibold ml-1">Sign Up</router-link>
              </p>
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
import Cookies from 'js-cookie'
import OpenFormFooter from '../../components/pages/OpenFormFooter'
import Testimonials from '../../components/pages/welcome/Testimonials'

export default {
  components: {
    OpenFormFooter,
    Testimonials
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/api/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.redirect()
    },

    redirect () {
      const intendedUrl = Cookies.get('intended_url')

      if (intendedUrl) {
        Cookies.remove('intended_url')
        this.$router.push({ path: intendedUrl })
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
