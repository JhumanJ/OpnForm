import { fetchAllWorkspaces } from "~/stores/workspaces.js"

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  
  // Get tokens from cookies
  const tokenValue = useCookie("token").value
  const adminTokenValue = useCookie("admin_token").value
  
  // Initialize the store with the tokens
  authStore.initStore(tokenValue, adminTokenValue)

  if (authStore.token && !authStore.user) {
    const workspaceStore = useWorkspacesStore()

    // Load user data and workspaces
    const [userDataResponse, workspacesResponse] = await Promise.all([
      useOpnApi("user"),
      fetchAllWorkspaces(),
    ])
    authStore.setUser(userDataResponse.data.value)
    workspaceStore.save(workspacesResponse.data.value)
  }
  authStore.initServiceClients()
})
