import {fetchAllWorkspaces} from "~/stores/workspaces.js";

export default defineNuxtRouteMiddleware(async(to, from) => {
  const authStore = useAuthStore()
  authStore.initStore(useCookie('token').value, useCookie('admin_token').value)

  if (authStore.token && !authStore.user) {
    const {data, error} = await useOpnApi('user')
    authStore.setUser(data.value)

    // Load workspaces
    const workspaceStore = useWorkspacesStore()
    const {data: workspacesData, error: workspacesError} = await fetchAllWorkspaces()
    workspaceStore.save(workspacesData.value)
  }
})
