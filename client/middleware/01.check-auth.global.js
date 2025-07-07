import { useQueryClient } from '@tanstack/vue-query'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()
  const { initServiceClients } = useAuthFlow()
  const { user } = useAuth()
  
  // Get tokens from cookies
  const tokenValue = useCookie("token").value
  const adminTokenValue = useCookie("admin_token").value
  
  // Initialize the store with the tokens
  authStore.initStore(tokenValue, adminTokenValue)

  // Call user query at top level and get data directly
  const { data: userData } = user()

  // If we have a token but no user data in cache, prefetch it
  if (authStore.token && !userData.value) {
    try {
      // Only prefetch user data - workspaces will be loaded client-side
      const { prefetchUser } = useAuth()
      
      await prefetchUser()

      // Initialize service clients after user data is loaded
      const userDataFromCache = queryClient.getQueryData(['user'])
      if (userDataFromCache) {
        initServiceClients(userDataFromCache)
      }
    } catch (error) {
      // If prefetch fails (e.g., invalid token), clear auth state
      console.warn('Auth prefetch failed:', error)
      if (error.status === 401) {
        authStore.clearTokens()
        queryClient.clear()
      }
    }
  } else if (authStore.token && userData.value) {
    // If we have both token and user data, make sure service clients are initialized
    initServiceClients()
  }
})
