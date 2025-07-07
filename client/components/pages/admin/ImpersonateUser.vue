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

const { list: fetchWorkspaces } = useWorkspaces()
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

    // Redirect to the dashboard.
    formsStore.set([])
    workspacesStore.set([])

    const { data: workspacesData } = await fetchWorkspaces()
    workspacesStore.set(workspacesData.value)
    formsStore.startLoading()
    formsStore.loadAll(workspacesStore.currentId)

    useAlert().success(`Impersonating ${userData.value.name}`)
    useRouter().push({ name: 'home' })
  })
    .catch((error) => {
      useAlert().error(error.data.message)
      loading.value = false
    })
}
</script>
