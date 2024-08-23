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
          <span v-if="canCancel">Manage Subscription & Invoices</span>
          <span else>Billing & Invoices</span>
        </UButton>
        <br><br>
        <v-button
          v-if="canCancel"
          color="white"
          :loading="cancelLoading"
          @click.prevent="cancelSubscription"
        >
          Cancel Subscription
        </v-button>
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
const cancelLoading = ref(false)
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

const canCancel = computed(() => {
  return user.value.subscriptions.some(sub => (sub.stripe_status === 'active') || (sub.stripe_status === 'trialing' && sub.ends_at == null))
})

const cancelSubscription = () => {
  cancelLoading.value = true
  opnFetch('/subscription').then((data) => {
    if (data && data.length) {
      window.profitwell('init_cancellation_flow', { subscription_id: data[0].stripe_id }).then(result => {
        // This means the customer either aborted the flow (i.e.
        // they clicked on "never mind, I don't want to cancel"), or
        // accepted a salvage attempt or salvage offer.
        // Thus, do nothing since they won't cancel.
        if (result.status === 'retained' || result.status === 'aborted') {
          console.log('Retained 🥳')
        } else {
          opnFetch('/subscription/' + data[0].id + '/cancel', { method: 'POST' })
            .then(() => {
              useAlert().success('Subscription cancelled. Sorry to see you leave 😢')
            })
            .catch(() => {
              useAlert().error('Sorry to see you leave 😢 We\'re currently having issues with subscriptions. Please ' +
                'send us a message via the livechat, and we will cancel your subscription.')
            }).finally(() => {
            cancelLoading.value = false
            useAmplitude().logEvent('subscription_cancelled')
            useCrisp().pushEvent('subscription_cancelled')

            // Now we need to reload workspace and user
            opnFetch('user').then((userData) => {
              authStore.setUser(userData)
            })
            fetchAllWorkspaces().then((workspaces) => {
              workspacesStore.set(workspaces.data.value)
            })
          })
        }
      })
    }
  }).finally(() => {
    useCrisp().pushEvent('subscription_cancelled')
    cancelLoading.value = false
  })
}

const openBillingDashboard = () => {
  billingLoading.value = true
  opnFetch('/subscription/billing-portal').then((data) => {
    const url = data.portal_url
    window.location = url
  }).catch((error) => {
    useAlert().error(error.data.message)
  }).finally(() => {
    billingLoading.value = false
  })
}
</script>
