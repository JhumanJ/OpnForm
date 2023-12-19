<template>
  <div>
    <forgot-password-modal :show="showForgotModal" @close="showForgotModal=false" />

    <form class="mt-4" @submit.prevent="login" @keydown="form.onKeydown($event)">
      <!-- Email -->
      <text-input name="email" :form="form" label="Email" :required="true" placeholder="Your email address" />

      <!-- Password -->
      <text-input native-type="password" placeholder="Your password"
                  name="password" :form="form" label="Password" :required="true"
      />

      <!-- Remember Me -->
      <div class="relative flex items-center my-5">
        <v-checkbox v-model="remember" class="w-full md:w-1/2" name="remember" size="small">
          Remember me
        </v-checkbox>

        <div class="w-full md:w-1/2 text-right">
          <a href="#" class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700" @click.prevent="showForgotModal=true">
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <v-button dusk="btn_login" :loading="form.busy">
        Log in to continue
      </v-button>

      <p class="text-gray-500 mt-4">
        Don't have an account?
        <a v-if="isQuick" href="#" class="font-semibold ml-1" @click.prevent="$emit('openRegister')">Sign Up</a>
        <router-link v-else :to="{name:'register'}" class="font-semibold ml-1">
          Sign Up
        </router-link>
      </p>
    </form>
  </div>
</template>

<script>
import ForgotPasswordModal from '../ForgotPasswordModal.vue'
import {opnFetch} from "~/composables/useOpnApi.js";

export default {
  name: 'LoginForm',
  components: {
    ForgotPasswordModal
  },
  props: {
    isQuick: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  setup () {
    const authStore = useAuthStore()
    return {
      authStore
    }
  },

  data: () => ({
    form: useForm({
      email: '',
      password: ''
    }),
    remember: false,
    showForgotModal: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const data = await this.form.post('login')

      // Save the token.
      this.authStore.setToken(data.token)

      const userData = await opnFetch('user')
      this.authStore.setUser(userData)

      // Redirect home.
      this.redirect()
    },

    redirect () {
      if (this.isQuick) {
        this.$emit('afterQuickLogin')
        return
      }

      const intendedUrlCookie = useCookie('intended_url')
      const router = useRouter()

      if (intendedUrlCookie.value) {
        router.push({ path: intendedUrlCookie.value })
        useCookie('intended_url').value = null
      } else {
        router.push({ name: 'home' })
      }
    }
  }
}
</script>
