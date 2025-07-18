import { useQueryClient } from '@tanstack/vue-query'
import { initServiceClients } from '~/composables/useAuthFlow'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()

  // Initialize tokens from cookies first
  authStore.initStore(
    useCookie('token').value,
    useCookie('admin_token').value,
  )

  // If no token, nothing to do
  if (!authStore.token) {
    return
  }

  // Check for already cached user data (from SSR or previous fetch)
  let userData = queryClient.getQueryData(['user'])

  // Fetch user & workspaces only if not cached yet
  if (!userData) {
    try {
      const userQuery = useAuth().user()
      const workspacesQuery = useWorkspaces().list()
      await Promise.all([userQuery.suspense(), workspacesQuery.suspense()])

      userData = queryClient.getQueryData(['user'])
    } catch (error) {
      // On 401, clear auth state
      if (error?.status === 401) {
        authStore.clearToken()
        queryClient.clear()
      }
      return
    }
  }

  // Initialize service clients on client side (no-op on server)
  if (userData) {
    initServiceClients(userData)
  }
})
