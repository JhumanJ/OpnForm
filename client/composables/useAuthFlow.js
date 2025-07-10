import { computed } from 'vue'
import { WindowMessageTypes, useWindowMessage } from "~/composables/useWindowMessage"
import { authApi } from "~/api"
import { useQueryClient } from '@tanstack/vue-query'
import { useAuth } from '~/composables/query/useAuth'
import { useWorkspaces } from '~/composables/query/useWorkspaces'

/**
 * Lightweight authentication check that doesn't require Vue Query context
 * Use this when you only need to check if user is authenticated
 */
export const useIsAuthenticated = () => {
  const authStore = useAuthStore()
  
  const isAuthenticated = computed(() => {
    return !!authStore.token
  })
  
  return { isAuthenticated }
}

export const useAuthFlow = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()
  const { logEvent } = useAmplitude()
  const router = useRouter()
  
  // Initialize composables at the top level
  const { user, invalidateUser, logout } = useAuth()
  const { list: listWorkspaces } = useWorkspaces()

  const userQuery = user()
  const workspacesQuery = listWorkspaces()
  const logoutMutation = logout()

  // Helper to get user data from cache (no API calls)
  const getCachedUserData = () => {
    return queryClient.getQueryData(['user'])
  }

  const hasActiveLicense = computed(() => {
    const userData = getCachedUserData()
    return userData !== null && userData !== undefined && userData.active_license !== null
  })

  // Service client initialization moved from auth store
  const initServiceClients = (userDataOverride = null) => {
    if (import.meta.server) return
    
    const userVal = userDataOverride || getCachedUserData()
    if (!userVal) return
    
    useAmplitude().setUser(userVal)
    useCrisp().setUser(userVal)
    // todo: set sentry user
  }

  /**
   * Core authentication logic used by both social and direct login
   * Now coordinates between Pinia store and TanStack Query
   */
  const authenticateUser = async ({ tokenData, source, isNewUser = false }) => {
    // 1. Set token in store first
    authStore.setToken(tokenData.token, tokenData.expires_in)

    // 2. Now that we have a token, refetch data with initialized queries
    const [workspacesResult] = await Promise.all([
      workspacesQuery.refetch(),
      // Invalidate user query to trigger fresh fetch with new token
      invalidateUser()
    ])
    const workspaces = workspacesResult.data

    // 3. Wait for user data to be fetched by TanStack Query
    // The user query will automatically cache and trigger onSuccess
    await userQuery.refetch()
    
    // Initialize service clients with user data
    initServiceClients(userQuery.data.value)

    // 4. Track analytics
    const eventName = isNewUser ? 'register' : 'login'
    logEvent(eventName, { source })
    
    try {
      // Check if GTM is available before using it
      const gtm = typeof useGtm === 'function' ? useGtm() : null
      if (gtm && typeof gtm.trackEvent === 'function') {
        gtm.trackEvent({
          event: eventName,
          source
        })
      }
    } catch (error) {
      console.error(error)
    }

    return { userData: userQuery.data.value, workspaces, isNewUser }
  }

  /**
   * Verify that authentication is complete and user data is loaded
   * Now uses TanStack Query for user data verification
   */
  const verifyAuthentication = async () => {
    // If we don't have a token, we're not authenticated
    if (!authStore.token) {
      return false
    }
    
    // If we have cached user data, we're good
    const cachedUserData = getCachedUserData()
    if (cachedUserData) {
      return true
    }
    
    // If we have a token but no cached user data, fetch it
    try {
      await userQuery.refetch()
      return true
    } catch (error) {
      console.error('Auth verification failed:', error)
      return false
    }
  }

  /**
   * Handle direct login with form validation
   */
  const loginWithCredentials = async (form, remember) => {
    const tokenData = await form.submit('post', '/login', { data: { remember: remember } })
    
    return authenticateUser({ 
      tokenData, 
      source: 'credentials'
    })
  }

  /**
   * Handle social login callback
   */
  const handleSocialCallback = async (provider, code, utmData) => {
    const tokenData = await authApi.oauth.callback(provider, { code, utm_data: utmData })
    
    // Send message to parent window if applicable
    if (window.opener && !window.opener.closed) {
      useWindowMessage(WindowMessageTypes.OAUTH_PROVIDER_CONNECTED).send(window.opener, {
        eventType: `${WindowMessageTypes.OAUTH_PROVIDER_CONNECTED}:${provider}`,
        useMessageChannel: false,
        waitForAcknowledgment: false
      })
    } 

    return authenticateUser({ 
      tokenData, 
      source: provider,
      isNewUser: tokenData.new_user
    })
  }

  /**
   * Handle user registration
   */
  const registerUser = async (form) => {
    // Register the user first
    const data = await form.submit('post', '/register')
    
    // Login the user
    const tokenData = await form.submit('post', '/login')
    
    const result = await authenticateUser({ 
      tokenData, 
      source: form.hear_about_us,
      isNewUser: true 
    })

    // Handle AppSumo license if present
    if (data.appsumo_license === false) {
      useAlert().error(
        "Invalid AppSumo license. This probably happened because this license was already" +
        " attached to another OpnForm account. Please contact support."
      )
    } else if (data.appsumo_license === true) {
      useAlert().success(
        "Your AppSumo license was successfully activated! You now have access to all the" +
        " features of the AppSumo deal."
      )
    }

    return { ...result, data }
  }

  /**
   * Handle logout flow
   * Uses TanStack Query cache clearing instead of manual store management
   */
  const handleLogout = async () => {
    try {
      await logoutMutation.mutateAsync()
    } catch (error) {
      // Even if API call fails, we still want to clear local state
      console.warn('Logout API call failed, but clearing local state anyway:', error)
    }
    
    // Clear all TanStack Query cache (replaces manual store clearing)
    queryClient.clear()
    
    // Navigate to login page
    router.push({ name: 'login' })
  }

  return {
    // Auth flow functions
    loginWithCredentials,
    handleSocialCallback,
    registerUser,
    verifyAuthentication,
    handleLogout,
  
    hasActiveLicense,
    
    // Helper functions
    initServiceClients
  }
} 