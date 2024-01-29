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

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import AppSumoBilling from '../../components/vendor/appsumo/AppSumoBilling.vue'

useOpnSeoMeta({
  title: 'Billing'
})
definePageMeta({
  middleware: "auth"
})

const authStore = useAuthStore()
let user = computed(() => authStore.user)
let billingLoading = false

const openBillingDashboard = () => {
  billingLoading = true
  opnFetch('/subscription/billing-portal').then((data) => {
    const url = data.portal_url
    window.location = url
  }).catch((error) => {
    useAlert().error(error.data.message)
  }).finally(() => {
    billingLoading = false
  })
}
</script>
