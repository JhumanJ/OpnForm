<template>
  <div class="flex flex-col min-h-screen">
    <div class="w-full md:max-w-3xl md:mx-auto px-4 mb-10 md:pb-20 md:pt-16 text-center flex-grow">
      <h1 class="text-4xl font-semibold">
        Thank you!
      </h1>
      <h4 class="text-xl mt-6">
        We're checking the status of your subscription please wait a moment...
      </h4>
      <div class="text-center">
        <Loader class="h-6 w-6 text-nt-blue mx-auto mt-20" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'

export default {
  layout: 'default',
  middleware: 'auth',

  setup () {
    useOpnSeoMeta({
      title: 'Subscription Success'
    })

    const authStore = useAuthStore()
    return {
      authStore,
      authenticated : computed(() => authStore.check),
      user : computed(() => authStore.user),
      crisp: useCrisp()
    }
  },

  data: () => ({
    interval: null
  }),

  mounted () {
    this.redirectIfSubscribed()
    this.interval = setInterval(() => this.checkSubscription(), 5000)
  },

  beforeUnmount () {
    clearInterval(this.interval)
  },

  methods: {
    async checkSubscription () {
      // Fetch the user.
      await this.authStore.fetchUser()
      this.redirectIfSubscribed()
    },
    redirectIfSubscribed () {
      if (this.user.is_subscribed) {
        useAmplitude().logEvent('subscribed', { plan: this.user.has_enterprise_subscription ? 'enterprise' : 'pro' })
        this.crisp.pushEvent('subscribed', { plan: this.user.has_enterprise_subscription ? 'enterprise' : 'pro' })
        this.$router.push({ name: 'home' })

        if (this.user.has_enterprise_subscription) {
          useAlert().success('Awesome! Your subscription to OpnForm is now confirmed! You now have access to all Enterprise ' +
            'features. No need to invite your teammates, just ask them to create a OpnForm account and to connect the same Notion workspace. Feel free to contact us if you have any question 🙌')
        } else {
          useAlert().success('Awesome! Your subscription to OpnForm is now confirmed! You now have access to all Pro ' +
            'features. Feel free to contact us if you have any question 🙌')
        }
      }
    }
  },

  computed: {}
}
</script>
