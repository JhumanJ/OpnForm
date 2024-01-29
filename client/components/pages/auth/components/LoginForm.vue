<template>
  <div>
    <forgot-password-modal :show="showForgotModal" @close="showForgotModal=false"/>

    <form class="mt-4" @submit.prevent="login" @keydown="form.onKeydown($event)">
      <!-- Email -->
      <text-input name="email" :form="form" label="Email" :required="true" placeholder="Your email address"/>

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
          <a href="#" class="text-xs hover:underline text-gray-500 sm:text-sm hover:text-gray-700"
             @click.prevent="showForgotModal=true">
            Forgot your password?
          </a>
        </div>
      </div>

      <!-- Submit Button -->
      <v-button dusk="btn_login" :loading="form.busy || loading">
        Log in to continue
      </v-button>

      <p class="text-gray-500 mt-4">
        Don't have an account?
        <a v-if="isQuick" href="#" class="font-semibold ml-1" @click.prevent="$emit('openRegister')">Sign Up</a>
        <NuxtLink v-else :to="{name:'register'}" class="font-semibold ml-1">
          Sign Up
        </NuxtLink>
      </p>
    </form>
  </div>
</template>

<script>
import ForgotPasswordModal from '../ForgotPasswordModal.vue'
import {opnFetch} from "~/composables/useOpnApi.js"
import {fetchAllWorkspaces} from "~/stores/workspaces.js"

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

  setup() {
    return {
      authStore: useAuthStore(),
      formsStore: useFormsStore(),
      workspaceStore: useWorkspacesStore()
    }
  },

  data: () => ({
    form: useForm({
      email: '',
      password: ''
    }),
    loading: false,
    remember: false,
    showForgotModal: false
  }),

  methods: {
    login() {
      // Submit the form.
      this.loading = true
      this.form.post('login').then(async (data) => {
        // Save the token.
        this.authStore.setToken(data.token)

        const [userDataResponse, workspacesResponse] = await Promise.all([opnFetch('user'), fetchAllWorkspaces()]);
        this.authStore.setUser(userDataResponse)
        this.workspaceStore.set(workspacesResponse.data.value)

        // Load forms
        this.formsStore.loadAll(this.workspaceStore.currentId)

        // Redirect home.
        this.redirect()
      }).catch((error) => {
        console.error(error)
      }).finally(() => {
        this.loading = false
      })
    },

    redirect() {
      if (this.isQuick) {
        this.$emit('afterQuickLogin')
        return
      }

      const intendedUrlCookie = useCookie('intended_url')
      const router = useRouter()

      if (intendedUrlCookie.value) {
        router.push({path: intendedUrlCookie.value})
        useCookie('intended_url').value = null
      } else {
        router.push({name: 'home'})
      }
    }
  }
}
</script>
