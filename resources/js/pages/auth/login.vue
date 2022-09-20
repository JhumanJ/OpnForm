<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:w-2/3 md:mx-auto md:max-w-md px-4">
        <h1 class="my-6">
          {{ $t('login') }}
        </h1>
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
          <!-- Email -->
          <text-input name="email" :form="form" :label="$t('email')" :required="true" />

          <!-- Password -->
          <text-input class="mt-8" native-type="password"
                      name="password" :form="form" :label="$t('password')" :required="true"
          />

          <!-- Remember Me -->
          <div class="relative flex items-center mt-8 mb-6">
            <v-checkbox v-model="remember" class="w-full md:w-1/2" name="remember">
              {{ $t('remember_me') }}
            </v-checkbox>

            <div class="w-full md:w-1/2 text-right">
              <router-link :to="{ name: 'password.request' }"
                          class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700"
              >
                {{ $t('forgot_password') }}
              </router-link>
            </div>
          </div>

          <!-- Submit Button -->
          <v-button class="w-full" dusk="btn_login" :loading="form.busy">
            {{ $t('login') }}
          </v-button>

          <p class="text-center text-gray-500 mt-4">
            No Account? <router-link :to="{name:'register'}">
              Register
            </router-link>
          </p>
        </form>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import Form from 'vform'
import Cookies from 'js-cookie'
import OpenFormFooter from '../../components/pages/OpenFormFooter'

export default {
  components: {
    OpenFormFooter
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
