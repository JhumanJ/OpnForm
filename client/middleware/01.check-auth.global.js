import { useQueryClient } from '@tanstack/vue-query'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  
  // Get tokens from cookies
  const tokenValue = useCookie("token").value
  const adminTokenValue = useCookie("admin_token").value
  
  // Initialize the store with the tokens
  authStore.initStore(tokenValue, adminTokenValue)

  if (authStore.token && !authStore.user) {
    const queryClient = useQueryClient()
    
    // Prefetch user data and workspaces using TanStack Query
    await Promise.all([
      queryClient.prefetchQuery({
        queryKey: ['user'],
        queryFn: () => opnFetch("/user"),
        staleTime: 5 * 60 * 1000
      }),
      queryClient.prefetchQuery({
        queryKey: ['workspaces', 'list'],
        queryFn: () => opnFetch("/open/workspaces/"),
        staleTime: 5 * 60 * 1000
      })
    ])

    // Set user data in auth store from query cache
    const userData = queryClient.getQueryData(['user'])
    if (userData) {
      authStore.setUser(userData)
    }

    // Keep workspace selection in Pinia for UI state management
    const workspacesData = queryClient.getQueryData(['workspaces', 'list'])
    if (workspacesData) {
      const workspaceStore = useWorkspacesStore()
      workspaceStore.save(workspacesData)
    }
  }
  authStore.initServiceClients()
})
