<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">Billing details</h3>
    <small class="text-gray-600">Manage your billing.</small>
    
    <div class="mt-4">
      <v-button color="gray" shade="light" :loading="billingLoading" @click.prevent="openBillingDashboard">
        Manage Subscription
      </v-button>
      <v-button color="red" class="mt-3" @click.prevent="cancelSubscription">
        Cancel Subscription
      </v-button>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import VButton from '../../components/common/Button'
import SeoMeta from '../../mixins/seo-meta'

export default {
  components: { VButton },
  scrollToTop: false,
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Billing',
    billingLoading: false
  }),

  methods: {
    cancelSubscription () {
      //   this.alertError('Sorry to see you leave 😢')
    },
    openBillingDashboard () {
      this.billingLoading = true
      axios.get('/api/subscription/billing-portal').then((response) => {
        const url = response.data.portal_url
        window.location = url
      }).finally(() => {
        this.billingLoading = false
      })
    }
  }
}
</script>
