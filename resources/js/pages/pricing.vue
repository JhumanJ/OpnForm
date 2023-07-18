<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-4xl md:mx-auto px-4 flex flex-wrap mb-10 md:pb-20 md:pt-16">
        <!--  For guests - we show basic features  -->
        <template v-if="!authenticated">
          <h1 id="pricing-title" class="sm:text-5xl">
            All of our core features are <span class="text-nt-blue">free</span>. You don't need to pay to create awesome
            OpnForms.
          </h1>
          <features class="py-16" :features-only="true" />
          <div class="flex justify-center w-full">
            <Motion
              :auto="[
                'scale-100 rotate-2',
                'scale-105 -rotate-2 -mt-5 mb-5',
              ]"
              :options="motionOptions"
            >
              <img alt="3d Rocket image, as pro subscription will take you to space" class="w-24"
                   :src="asset('img/icons/rocket.png')"
              >
            </Motion>
          </div>
        </template>
        <div v-else class="w-full mt-5">
          <h2 class="text-center text-5xl">
            <span class="relative inline-block">
              <svg viewBox="0 0 52 24" fill="currentColor"
                   class="text-nt-blue-light absolute top-0 left-0 z-0 hidden w-32 -mt-8 -ml-20 text-blue-gray-100 lg:w-32 lg:-ml-28 lg:-mt-10 sm:block"
              >
                <defs>
                  <pattern id="27df4f81-c854-45de-942a-fe90f7a300f9" x="0" y="0" width=".135" height=".30">
                    <circle cx="1" cy="1" r=".7" />
                  </pattern>
                </defs>
                <rect fill="url(#27df4f81-c854-45de-942a-fe90f7a300f9)" width="52" height="24" />
              </svg>
              <span class="relative">Do</span>
            </span>
            even more with OpnForms <span v-if="!user || !user.is_subscribed" class="text-nt-blue">Pro</span><span
              v-else class="text-nt-blue"
            >Enterprise</span>
          </h2>
          <h4 class="text-center mt-6 text-lg">
            We're happy to have you as a <span v-if="user && !user.has_enterprise_subscription">Pro</span><span v-else>Enterprise</span>
            subscriber. If you're having any issue with OpnForms, or if you have a
            feature request, please <a href="#" @click.prevent="contactUs">contact us</a>.
          </h4>
        </div>
        <pricing-table v-if="!authenticated" />
        <div v-if="!authenticated" class="w-full mt-16 text-center">
          <h2>Create your first Form <span class="text-nt-blue">now</span></h2>
          <h4 class="my-5 text-xl">
            OpnForms is the best and easiest solution to create forms for Opn users. You don't need take our word
            for it, just try for free!
          </h4>
          <v-button :to="{name:'register'}">
            Create a Form
          </v-button>
        </div>
        <div v-else class="w-full">
          <h2 class="mt-16 mb-5 text-3xl text-center">
            Our different plans
          </h2>
          <p>
            With the free plan you can create as many forms as you need and there is no limit on the number of responses
            you can receive.
            The Pro plan offers tons of features to make your forms beautiful and even more powerful. Finally, the
            Enterprise plan increases the limits
            of the Pro plan (larger file uploads, unlimited workspaces), allows you to collaborate (unlimited number of
            users), to your own domain, and comes with
            priority support.
          </p>
          <pricing-table />
        </div>
      </div>
      <open-form-footer />
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Motion from 'tinymotion'
import { mapGetters } from 'vuex'
import OpenFormFooter from '../components/pages/OpenFormFooter.vue'
import Features from '../components/pages/welcome/Features.vue'
import PricingTable from '../components/pages/pricing/PricingTable.vue'
import SeoMeta from '../mixins/seo-meta.js'

export default {
  components: { Motion, OpenFormFooter, Features, PricingTable },

  mixins: [SeoMeta],
  layout: 'default',

  props: {},

  data: () => ({
    metaTitle: 'Pricing',
    metaDescription: 'All of our core features are free, and there is no quantity limit. You can also created more advanced and customized forms with OpnForms Pro.',
    checkoutLoading: false,
    billingLoading: false,
    monthly: true
  }),

  mounted () {
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    }),

    motionOptions () {
      return {
        repeat: true, // infinite animation until stopped
        duration: 1000, // default duration. might be overridden by duration-{value} Tailwind class,
        ease: 'linear'
      }
    }
  },
  methods: {
    openBilling () {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        this.billingLoading = false
        const url = response.data.portal_url
        window.location = url
      })
    },
    openCheckout (plan) {
      this.checkoutLoading = true
      axios.get('/api/subscription/new/' + plan + '/' + (this.monthly ? 'monthly' : 'yearly') + '/checkout/with-trial').then((response) => {
        window.location = response.data.checkout_url
      }).catch((error) => {
        this.alertError(error.response.data.message)
      }).finally(() => {
        this.checkoutLoading = false
      })
    },
    contactUs () {
      window.$crisp.push(['do', 'chat:show'])
      window.$crisp.push(['do', 'chat:open'])
    }
  }
}
</script>

<style scoped lang='scss'>
#pricing-title {
  line-height: 1.2 !important;
}
</style>
