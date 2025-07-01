<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Billing Details</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Manage your billing. Download invoices, update your plan, or cancel it at any time.
        </p>
      </div>
    </div>

    <!-- Billing Management -->
    <template v-if="user.has_customer_id">
      <div class="space-y-4 rounded-lg border border-neutral-200 p-4 shadow-sm">
        <p
          v-if="usersCount"
          class="text-neutral-600"
        >
          You currently have <span class="font-medium">{{ usersCount }} users</span> in your different workspaces.
        </p>

        <div class="flex flex-wrap gap-3">
          <UButton
            icon="i-heroicons-credit-card"
            :loading="billingLoading"
            :to="{ name: 'redirect-billing-portal' }"
            target="_blank"
          >
            <span v-if="canCancel">Manage Subscription & Invoices</span>
            <span v-else>Billing & Invoices</span>
          </UButton>

          <UButton
            v-if="canCancel"
            color="error"
            variant="soft"
            :loading="cancelLoading"
            @click.prevent="cancelSubscription"
          >
            Cancel Subscription
          </UButton>
        </div>
      </div>
    </template>

    <!-- AppSumo Billing -->
    <AppSumoBilling />
  </div>
</template>

<script setup>
import { computed } from "vue"
import { useAuthStore } from "../../../stores/auth"
import AppSumoBilling from "../../vendor/appsumo/AppSumoBilling.vue"

const authStore = useAuthStore()
const alert = useAlert()

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
      alert.error(error.data.message)
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
              alert.success('Subscription cancelled. Sorry to see you leave 😢')
            })
            .catch(() => {
              alert.error('Sorry to see you leave 😢 We\'re currently having issues with subscriptions. Please ' +
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
</script> 