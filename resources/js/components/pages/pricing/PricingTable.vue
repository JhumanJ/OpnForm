<template>
  <div class="w-full">
    <section class="relative">
      <div class="absolute inset-0 grid" aria-hidden="true">
        <div class="bg-gray-100"></div>
        <div class="bg-white"></div>
      </div>

      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto bg-white shadow-xl rounded-3xl ring-1 ring-gray-200 lg:flex isolate">
          <div class="p-8 sm:p-8 lg:flex-auto">
            <h3 class="text-3xl font-semibold tracking-tight text-gray-950">
              Pro Plan
            </h3>
            <p class="mt-2 text-base font-medium leading-7 text-gray-600">
              OpnForm Pro offers empowering features tailored to the advanced needs of teams and creators. Enjoy our free 3-day trial!
            </p>

            <div class="flex items-center mt-6 gap-x-4">
              <h4 class="flex-none text-sm font-semibold leading-6 tracking-widest text-gray-400 uppercase">
                What's included
              </h4>
              <div class="flex-auto h-px bg-gray-200"></div>
            </div>

            <ul role="list" class="grid grid-cols-1 gap-4 mt-4 text-sm font-medium leading-6 text-gray-900 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-2">
              <li v-for="(title, i) in pricingInfo" :key="i" class="flex gap-x-3">
                <svg aria-hidden="true" class="w-5 h-5 shrink-0 stroke-blue-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 6L9 17L4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                {{ title }}
              </li>
            </ul>
          </div>

          <div class="p-2 -mt-2 lg:mt-0 lg:w-full lg:max-w-md lg:flex-shrink-0">
            <div class="py-10 text-center rounded-2xl bg-gray-50 ring-1 ring-inset ring-gray-900/5 lg:flex lg:flex-col lg:justify-center lg:py-12">
              <div class="max-w-xs px-8 mx-auto space-y-6">
                <div class="flex items-center justify-center mb-10">
                  <monthly-yearly-selector v-model="isYearly" />
                </div><!-- lg+ -->

                <p class="flex flex-col items-center">
                  <span class="text-6xl font-semibold tracking-tight text-gray-950">
                    <template v-if="isYearly">$16</template>
                    <template v-else>$19</template>
                  </span>
                  <span class="text-sm font-medium leading-6 text-gray-600">
                    per month
                  </span>
                </p>

                <div class="flex justify-center">
                  <v-button v-if="!authenticated" class="mr-1" :to="{ name: 'register' }" :arrow="true">
                    Start free trial
                  </v-button>
                  <v-button v-else-if="authenticated && user.is_subscribed" class="mr-1" @click.prevent="openBilling" :arrow="true">
                    View Billing
                  </v-button>
                  <v-button v-else class="mr-1" @click.prevent="openCustomerCheckout('default')" :arrow="true">
                    Start free trial
                  </v-button>
                </div>
                <p class="text-xs font-medium leading-5 text-gray-600">
                  Invoices and receipts available for easy company reimbursement.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <checkout-details-modal :show="showDetailsModal" :yearly="isYearly" :plan="selectedPlan"
                            @close="showDetailsModal=false"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
import MonthlyYearlySelector from './MonthlyYearlySelector.vue'
import CheckoutDetailsModal from './CheckoutDetailsModal.vue'

export default {
  components: {
    MonthlyYearlySelector,
    CheckoutDetailsModal,
  },
  props: {},
  data: () => ({
    isYearly: true,
    selectedPlan: 'pro',
    showDetailsModal: false,

    pricingInfo: [
      'Form confirmation emails',
      'Slack notifications',
      'Discord notifications',
      'Editable submissions',
      'Custom domain (soon)',
      'Custom code',
      'Larger file uploads (50mb)',
      'Remove OpnForm branding',
      'Priority support'
    ]
  }),

  methods: {
    openCustomerCheckout (plan) {
      this.selectedPlan = plan
      this.showDetailsModal = true
    },
    openBilling () {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        this.billingLoading = false
        const url = response.data.portal_url
        window.location = url
      })
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check',
      user: 'auth/user'
    })
  }
}
</script>
