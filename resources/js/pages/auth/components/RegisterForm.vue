<template>
  <div>
    <form class="mt-4" @submit.prevent="register" @keydown="form.onKeydown($event)">
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
                  name="password_confirmation" :label="$t('confirm_password')"
      />

      <checkbox-input :form="form" name="agree_terms" :required="true">
        <template #label>
          I agree with the <router-link :to="{name:'terms-conditions'}" target="_blank">
            Terms and conditions
          </router-link> and <router-link :to="{name:'privacy-policy'}" target="_blank">
            Privacy policy
          </router-link> of the website and I accept them.
        </template>
      </checkbox-input>

      <!-- Submit Button -->
      <v-button :loading="form.busy">
        Create an account
      </v-button>

      <p class="text-gray-500 mt-4">
        Already have an account?
        <a v-if="isQuick" href="#" class="font-semibold ml-1" @click.prevent="$emit('openLogin')">Log In</a>
        <router-link v-else :to="{name:'login'}" class="font-semibold ml-1">
          Log In
        </router-link>
      </p>
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import { initCrisp } from '../../../middleware/check-auth.js'

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

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      agree_terms: false,
      appsumo_license: null
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

  mounted () {
    // Set appsumo license
    if (this.$route.query.appsumo_license !== undefined && this.$route.query.appsumo_license) {
      this.form.appsumo_license = this.$route.query.appsumo_license
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

        initCrisp(data)
        this.$crisp.push(['set', 'session:event', [[['register', {}, 'blue']]]])

        // AppSumo License
        if (data.appsumo_license === false) {
          this.alertError('Invalid AppSumo license. This probably happened because this license was already' +
            ' attached to another OpnForm account. Please contact support.')
        } else if (data.appsumo_license === true) {
          this.alertSuccess('Your AppSumo license was successfully activated! You now have access to all the' +
            ' features of the AppSumo deal.')
        }

        // Redirect
        if (this.isQuick) {
          this.$emit('afterQuickLogin')
        } else {
          this.$router.push({ name: 'forms.create' })
        }
      }
    }
  }
}
</script>
