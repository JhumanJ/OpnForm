<template>
  <div>
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

      <checkbox-input :form="form" name="agree_terms" :required="true">
        <template #label>
          I agree with the <router-link :to="{name:'terms-conditions'}" target="_blank">Terms and conditions</router-link> and <router-link :to="{name:'privacy-policy'}" target="_blank">Privacy policy</router-link> of the website and I accept them.
        </template>
      </checkbox-input>

      <!-- Submit Button -->
      <v-button :loading="form.busy">Create an account</v-button>

      <p class="text-gray-500 mt-4">
        Already have an account?  
        <a href="#" v-if="isQuick" @click.prevent="$emit('openLogin')" class="font-semibold ml-1">Log In</a>
        <router-link v-else :to="{name:'login'}" class="font-semibold ml-1">Log In</router-link>
      </p>

      <!-- GitHub Register Button -->
      <login-with-github />
    </form>
  </div>
</template>

<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub.vue'
import SelectInput from '../../../components/forms/SelectInput.vue'
import { initCrisp } from '../../../middleware/check-auth.js'

export default {
  name: 'RegisterForm',
  components: {
    SelectInput,
    LoginWithGithub,
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
        
        initCrisp(data)
        this.$crisp.push(['set', 'session:event', [[['register', {}, 'blue']]]])

        // Redirect
        if(this.isQuick){
          this.$emit('afterQuickLogin')
        }else{
          this.$router.push({ name: 'forms.create' })
        }
      }
    }
  }
}
</script>
