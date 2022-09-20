<template>
  <card title="Billing" class="bg-gray-50 dark:bg-notion-dark-light">
    <v-button color="gray" shade="light" :loading="billingLoading" @click.prevent="openBillingDashboard">
      Manage Subscription
    </v-button>
    <v-button color="red" class="mt-3" @click.prevent="cancelSubscription">
      Cancel Subscription
    </v-button>
  </card>
</template>

<script>
import axios from 'axios'
import VButton from '../../components/common/Button'

export default {
  components: { VButton },
  scrollToTop: false,

  metaInfo () {
    return { title: 'Billing' }
  },

  data: () => ({
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
