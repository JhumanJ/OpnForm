<template>
  <UButton
    size="sm"
    color="white"
    icon="i-heroicons-eye-16-solid"
    @click="impersonate"
  >
    Impersonate User
  </UButton>
</template>

<script setup>
const props = defineProps({
  user: { type: Object, required: true }
})

const authStore = useAuthStore()
const formsStore = useFormsStore()
const workspacesStore = useWorkspacesStore()

let loading = ref(false)

const impersonate = () => {
  loading = true
  authStore.startImpersonating()
  opnFetch(`/moderator/impersonate/${props.user.id}`).then(async (data) => {
    loading = false

    // Save the token.
    authStore.setToken(data.token, false)

    // Fetch the user.
    const userData = await opnFetch('user')
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
      loading = false
    })
}
</script>
