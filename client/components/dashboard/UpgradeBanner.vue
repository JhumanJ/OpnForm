<template>
  <UAlert
    v-if="showBanner"
    class="mt-8 p-4"
    icon="i-heroicons-sparkles-solid"
    color="primary"
    variant="subtle"
    title="Discover our Pro plan"
    description="Remove OpnForm branding, customize forms further, use your custom domain, integrate with your favorite tools, invite users, and more!"
    :actions="[
      {
        label: 'Try for free',
        click: openSubscriptionModal
      },
      {
        label: 'Close',
        color: 'neutral',
        variant: 'outline',
        click: dismissBanner
      }
    ]"
  />
</template>

<script setup>
import { useCookie } from '#app'

const COOKIE_NAME = 'upgrade_banner_dismissed'
const COOKIE_EXPIRY_DAYS = 7

// Composables
const subscriptionModalStore = useSubscriptionModalStore()

// Cookie state
const dismissedCookie = useCookie(COOKIE_NAME, {
  default: () => false,
  maxAge: COOKIE_EXPIRY_DAYS * 24 * 60 * 60 * 1000, // 7 days in milliseconds
  sameSite: 'lax',
  secure: true,
  httpOnly: false
})

// Computed
const showBanner = computed(() => !dismissedCookie.value)

// Methods
const openSubscriptionModal = () => {
  subscriptionModalStore.openModal()
}

const dismissBanner = () => {
  dismissedCookie.value = true
}
</script> 