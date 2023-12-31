<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">
      Billing details
    </h3>

    <template v-if="user.has_customer_id">
      <small class="text-gray-600">Manage your billing. Download invoices, update your plan, or cancel it at any
        time.</small>

      <div class="mt-4">
        <v-button color="gray" shade="light" :loading="billingLoading" @click.prevent="openBillingDashboard">
          Manage Subscription
        </v-button>
      </div>
    </template>

    <app-sumo-billing class="mt-4" />
  </div>
</template>

<script>
import axios from 'axios'
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import VButton from '~/components/global/VButton.vue'
import SeoMeta from '../../mixins/seo-meta.js'
import AppSumoBilling from '../../components/vendor/appsumo/AppSumoBilling.vue'

export default {
  components: { AppSumoBilling, VButton },
  mixins: [SeoMeta],
  scrollToTop: false,

  setup () {
    const authStore = useAuthStore()
    return {
      user : computed(() => authStore.user)
    }
  },

  data: () => ({
    metaTitle: 'Billing',
    billingLoading: false
  }),

  methods: {
    openBillingDashboard () {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        const url = response.data.portal_url
        window.location = url
      }).catch((error) => {
        useAlert().error(error.response.data.message)
      }).finally(() => {
        this.billingLoading = false
      })
    }
  },

  computed: {}
}
</script>
