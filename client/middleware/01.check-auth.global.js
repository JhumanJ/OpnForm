import {fetchAllWorkspaces} from "~/stores/workspaces.js";
import {opnFetch} from "~/composables/useOpnApi.js";

export default defineNuxtRouteMiddleware(async(to, from) => {
  const authStore = useAuthStore()
  authStore.initStore(useCookie('token').value, useCookie('admin_token').value)

  if (authStore.token && !authStore.user) {
    const workspaceStore = useWorkspacesStore()

    // Load user data and workspaces
    const [userDataResponse, workspacesResponse] = await Promise.all([useOpnApi('user'), fetchAllWorkspaces()]);
    authStore.setUser(userDataResponse.data.value)
    workspaceStore.save(workspacesResponse.data.value)
  }
})
