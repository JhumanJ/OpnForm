<template>
  <div class="flex flex-col items-center justify-center p-10">
    <template v-if="loading">
      <Loader class="h-6 w-6 mb-4" />
      <p class="text-gray-600">
        Processing your connection...
      </p>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAlert } from '~/composables/useAlert'
import { useWindowMessage, WindowMessageTypes } from "~/composables/useWindowMessage"

// Define meta for auth middleware
definePageMeta({
  middleware: "auth",
})

// Use composables
const route = useRoute()
const router = useRouter()
const alert = useAlert()

// State
const loading = ref(true)
const errorMessage = ref(null)

async function handleCallback() {
  loading.value = true
  errorMessage.value = null
  const code = route.query.code
  const service = route.params.service

  if(!code || !service) {
    errorMessage.value = "Missing code or service parameter."
    alert.error(errorMessage.value)
    router.push('/settings/connections')
    return
  }

  try {
    const data = await opnFetch(`/settings/providers/callback/${service}`, {
      method: 'POST',
      params: { code }
    })

    if (window.opener && !window.opener.closed) {
      try {
          await useWindowMessage(WindowMessageTypes.OAUTH_PROVIDER_CONNECTED).send(window.opener, {
              eventType: `${WindowMessageTypes.OAUTH_PROVIDER_CONNECTED}:${service}`,
              useMessageChannel: false,
              waitForAcknowledgment: false,
              targetOrigin: window.location.origin
          })
      } catch {
          // Silently handle error when sending window message - continue flow regardless
      }
    }

    // Get autoClose preference from the API response data
    const shouldAutoClose = data?.autoClose === true
    alert.success('Account connected successfully.')
    
    // Close window if autoClose is set from API data, otherwise redirect
    if (shouldAutoClose) {
      window.close()
      // Add a fallback check in case window.close is blocked
      setTimeout(() => {
        if (!window.closed) {
            console.warn('[CallbackPage] window.close() did not execute or was blocked.')
            // Optionally, redirect here as a fallback if close fails?
            // router.push('/settings/connections');
        }
      }, 500) // Check after 500ms
    } else {
      router.push('/settings/connections')
    }

  } catch (error) {
    try {
      errorMessage.value = error?.data?.message || "An error occurred while connecting the account."
      alert.error(errorMessage.value)
    } catch {
      errorMessage.value = "An unknown error occurred while connecting the account."
      alert.error(errorMessage.value)
    }
    router.push('/settings/connections')
  }
}

onMounted(() => {
  handleCallback()
})
</script> 