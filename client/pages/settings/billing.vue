<template>
  <div>
    <h3 class="font-semibold text-2xl text-gray-900">
      Billing details
    </h3>

    <template v-if="user.has_customer_id">
      <small class="text-gray-600">Manage your billing. Download invoices, update your plan, or cancel it
        at any time.</small>

      <div class="mt-4 flex flex-wrap gap-2 w-full border shadow rounded-lg p-4 items-center">
        <p
          v-if="usersCount"
          class="text-gray-500 flex-grow"
        >
          You currently have <span class="font-medium">{{ usersCount }} users</span> in your different workspaces.
        </p>
        <UButton
          color="gray"
          icon="i-heroicons-credit-card"
          :loading="billingLoading"
          @click="openBillingDashboard"
        >
          Manage Subscription
        </UButton>
      </div>
    </template>

    <app-sumo-billing class="mt-4" />
  </div>
</template>

<script setup>
import { computed } from "vue"
import { useAuthStore } from "../../stores/auth"
import AppSumoBilling from "../../components/vendor/appsumo/AppSumoBilling.vue"

useOpnSeoMeta({
  title: "Billing",
})
definePageMeta({
  middleware: "auth",
})

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const billingLoading = ref(false)
const usersCount = ref(0)

onMounted(() => {
  loadUsersCount()
})

const loadUsersCount = () => {
  opnFetch("/subscription/users-count")
    .then((data) => {
      usersCount.value = data.count
    })
    .catch((error) => {
      useAlert().error(error.data.message)
    })
}

const openBillingDashboard = () => {
  billingLoading.value = true
  opnFetch("/subscription/billing-portal")
    .then((data) => {
      const url = data.portal_url
      window.location = url
    })
    .catch((error) => {
      useAlert().error(error.data.message)
    })
    .finally(() => {
      billingLoading.value = false
    })
}
</script>
