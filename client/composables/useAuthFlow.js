import { computed } from 'vue'
import { WindowMessageTypes, useWindowMessage } from "~/composables/useWindowMessage"
import { authApi } from "~/api"
import { useQueryClient } from '@tanstack/vue-query'

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

/**
 * Initialize service clients without requiring Vue Query context
 * Safe to call from middleware or anywhere outside setup context
 */
export const initServiceClients = (userData) => {
  if (import.meta.server) return
  if (!userData) return
  
  useAmplitude().setUser(userData)
  useCrisp().setUser(userData)
  // todo: set sentry user
}

export const useAuthFlow = () => {
  const authStore = useAuthStore()
  const queryClient = useQueryClient()
  const { logEvent } = useAmplitude()
  const router = useRouter()

  // Initialize Vue Query hooks but don't create instances
  const { invalidate: invalidateWorkspaces } = useWorkspaces()
  const { invalidateUser, logout: logoutMutationFactory, login, register } = useAuth()
  
  // Prepare logout mutation ahead of time within a valid Vue context
  const logoutMutation = logoutMutationFactory()
  const loginMutation = login()
  const registerMutation = register()


  // Helper to get user data from cache (no API calls)
  const getCachedUserData = () => {
    return queryClient.getQueryData(['user'])
  }

  const hasActiveLicense = computed(() => {
    const userData = getCachedUserData()
    return userData !== null && userData !== undefined && userData.active_license !== null
  })

  // Service client initialization moved to external function

  /**
   * Core authentication logic used by both social and direct login
   * Now coordinates between Pinia store and TanStack Query
   */
  const authenticateUser = async (tokenData, source, isNewUser = false) => {
    // 1. Set token in store first
    authStore.setToken(tokenData.token, tokenData.expires_in)

    // 2. If user data is provided in the token response, cache it immediately
    // This ensures that the user query has data immediately after registration
    const promises = [invalidateWorkspaces()]
    if (tokenData.user) {
      queryClient.setQueryData(['user'], tokenData.user)
      initServiceClients(tokenData.user)
    } else {
      promises.push(invalidateUser())
    }

    // 3. Invalidate queries to trigger a refetch in any active components
    await Promise.all(promises)

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
  }


  /**
   * Handle direct login with form validation
   */
  const loginWithCredentials = async (data) => {
    const tokenData = await loginMutation.mutateAsync(data)
    
    return authenticateUser(
      tokenData, 
      'credentials'
    )
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

    return authenticateUser( 
      tokenData, 
      provider,
      tokenData.new_user
    )
  }

  /**
   * Handle user registration
   */
  const registerUser = async (data) => {
    const tokenData = await registerMutation.mutateAsync(data)
    
    await authenticateUser(
      tokenData, 
      data.hear_about_us,
      true 
    )

    // Handle AppSumo license if present
    if (tokenData.appsumo_license === false) {
      useAlert().error(
        "Invalid AppSumo license. This probably happened because this license was already" +
        " attached to another OpnForm account. Please contact support."
      )
    } else if (tokenData.appsumo_license === true) {
      useAlert().success(
        "Your AppSumo license was successfully activated! You now have access to all the" +
        " features of the AppSumo deal."
      )
    }

    return { data: tokenData }
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
    handleLogout,
  
    hasActiveLicense
  }
} 