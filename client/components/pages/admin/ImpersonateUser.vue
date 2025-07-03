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

const props = defineProps({
  user: { type: Object, required: true }
})

const authStore = useAuthStore()
const formsStore = useFormsStore()
const workspacesStore = useWorkspacesStore()

const loading = ref(false)

const impersonate = () => {
  loading.value = true
  authStore.startImpersonating()
  adminApi.impersonate(props.user.id).then(async (data) => {
    loading.value = false

    // Save the token with its expiration time.
    authStore.setToken(data.token, data.expires_in)

    // Fetch the user.
    const userData = await authApi.user.get()
    authStore.setUser(userData)

    // Redirect to the dashboard.
    formsStore.set([])
    workspacesStore.set([])

    const workspaces = await fetchAllWorkspaces()
    workspacesStore.set(workspaces.data.value)
    formsStore.startLoading()
    formsStore.loadAll(workspacesStore.currentId)

    useAlert().success(`Impersonating ${authStore.user.name}`)
    useRouter().push({ name: 'home' })
  })
    .catch((error) => {
      useAlert().error(error.data.message)
      loading.value = false
    })
}
</script>
