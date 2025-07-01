<template>
  <UTooltip
    v-if="isImpersonating"
    text="Stop Impersonation"
  >
    <UButton
      color="neutral"
      variant="outline"
      :loading="loading"
      icon="i-heroicons-arrow-left-start-on-rectangle-20-solid"
      @click="reverseImpersonation"
    />
  </UTooltip>
</template>

<script setup>
const authStore = useAuthStore()
const workspacesStore = useWorkspacesStore()
const router = useRouter()

const isImpersonating = computed(() => authStore.isImpersonating)
const loading = ref(false)

async function reverseImpersonation() {
  loading.value = true
  authStore.stopImpersonating()

  // Fetch the user.
  const userData = await opnFetch('user')
  authStore.setUser(userData)
  const workspaces = await fetchAllWorkspaces()
  workspacesStore.set(workspaces.data.value)
  router.push({ name: 'admin' })
  loading.value = false
}
</script>

