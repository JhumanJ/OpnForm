<template>
  <div class="flex flex-col items-center justify-center min-h-screen gap-4">
    <Loader class="w-8 h-8 text-blue-500" />
    <p class="text-gray-500">
      Redirecting to billing portal...
    </p>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth'
})

onMounted(async () => {
  try {
    const { portal_url } = await opnFetch('/subscription/billing-portal')
    if (!portal_url) {
      throw new Error('No portal URL returned')
    }
    window.location.href = portal_url
  } catch {
    useAlert().error('Unable to access billing portal. Please try again or contact support.')
    setTimeout(() => {
      navigateTo({ name: 'settings-billing' })
    }, 2000)
  }
})
</script> 