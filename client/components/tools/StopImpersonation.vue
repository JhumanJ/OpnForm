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
import { authApi } from "~/api/auth"

const authStore = useAuthStore()
const router = useRouter()

const isImpersonating = computed(() => authStore.isImpersonating)
const loading = ref(false)

async function reverseImpersonation() {
  loading.value = true
  authStore.stopImpersonating()

  // Fetch the user.
      await authApi.user.get()
   useAuth().invalidateUser()
  useWorkspaces().invalidateAll()
  router.push({ name: 'admin' })
  loading.value = false
}
</script>

