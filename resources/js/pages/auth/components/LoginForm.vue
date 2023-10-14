<template>
  <div>
    <forgot-password-modal :show="showForgotModal" @close="showForgotModal=false" />

    <form @submit.prevent="login" @keydown="form.onKeydown($event)" class="mt-4">
      <!-- Email -->
      <text-input name="email" :form="form" :label="$t('email')" :required="true" placeholder="Your email address" />

      <!-- Password -->
      <text-input native-type="password" placeholder="Your password"
                  name="password" :form="form" :label="$t('password')" :required="true"
      />

      <!-- Remember Me -->
      <div class="relative flex items-center my-5">
        <v-checkbox v-model="remember" class="w-full md:w-1/2" name="remember" size="small">
          {{ $t('remember_me') }}
        </v-checkbox>

        <div class="w-full md:w-1/2 text-right">
          <a href="#" @click.prevent="showForgotModal=true" class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700">
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <v-button dusk="btn_login" :loading="form.busy">Log in to continue</v-button>

      <p class="text-gray-500 mt-4">
        Don't have an account?  
        <a href="#" v-if="isQuick" @click.prevent="$emit('openRegister')" class="font-semibold ml-1">Sign Up</a>
        <router-link v-else :to="{name:'register'}" class="font-semibold ml-1">Sign Up</router-link>
      </p>
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import Cookies from 'js-cookie'
import OpenFormFooter from '../../../components/pages/OpenFormFooter.vue'
import Testimonials from '../../../components/pages/welcome/Testimonials.vue'
import ForgotPasswordModal from '../ForgotPasswordModal.vue'

export default {
  name: 'LoginForm',
  components: {
    OpenFormFooter,
    Testimonials,
    ForgotPasswordModal
  },
  props: {
    isQuick: {
      type: Boolean,
      required: false,
      default: false
    }
  }, 

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false,
    showForgotModal: false
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
      if(this.isQuick){
        this.$emit('afterQuickLogin')
        return
      }

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
