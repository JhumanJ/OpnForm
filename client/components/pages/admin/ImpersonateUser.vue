<template>
  <UButton
    variant="outline"
    icon="i-heroicons-eye-16-solid"
    :loading="loading"
    @click="impersonate"
    label="Impersonate User"
  />
</template>

<script setup>
import { adminApi, authApi } from '~/api'
import { useQueryClient } from '@tanstack/vue-query'
import { useWorkspaces } from "~/composables/query/useWorkspaces"
import { useForms } from "~/composables/query/useForms"

const props = defineProps({
  user: { type: Object, required: true }
})

const authStore = useAuthStore()
const queryClient = useQueryClient()

const loading = ref(false)

const { invalidateAll: invalidateWorkspaces } = useWorkspaces()
const { invalidateAll: invalidateForms } = useForms()
const { user } = useAuth()
const { data: userData } = user()

const impersonate = () => {
  loading.value = true
  authStore.startImpersonating()
  adminApi.impersonate(props.user.id).then(async (data) => {
    loading.value = false

    // Save the token with its expiration time.
    authStore.setToken(data.token, data.expires_in)

    // Fetch the user.
    await authApi.user.get()
    useAuth().invalidateUser()

    // Clear all query cache and invalidate to refetch fresh data for the impersonated user
    queryClient.clear()
    await invalidateWorkspaces()
    await invalidateForms()

    useAlert().success(`Impersonating ${userData.value.name}`)
    useRouter().push({ name: 'home' })
  })
    .catch((error) => {
      useAlert().error(error.data.message)
      loading.value = false
    })
}
</script>
