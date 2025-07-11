import { WindowMessageTypes, useWindowMessage } from "~/composables/useWindowMessage"
import { authApi } from "~/api"

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
}

export const useAuthFlow = () => {
  const authStore = useAuthStore()
  const { logEvent } = useAmplitude()
  const router = useRouter()

  /**
   * Core authentication success handler  
   * Coordinates token storage, analytics, and UI feedback (cache management handled by useAuth)
   */
  const handleAuthSuccess = async (tokenData, source, isNewUser = false) => {
    // 1. Set token in store
    authStore.setToken(tokenData.token, tokenData.expires_in)

    // 2. Initialize service clients if user data is provided
    if (tokenData.user) {
      initServiceClients(tokenData.user)
    }

    // 3. Handle AppSumo license feedback (registration-specific)
    if (isNewUser && tokenData.appsumo_license !== undefined) {
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
    }

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

    return handleAuthSuccess( 
      tokenData, 
      provider,
      tokenData.new_user
    )
  }

  /**
   * Handle logout coordination
   * Token clearing and cache management handled by useAuth logout mutation
   */
  const handleLogout = async () => {
    // Clear auth store
    authStore.clearToken()
    
    // Navigate to login page
    router.push({ name: 'login' })
  }

  return {
    // Auth flow functions
    handleAuthSuccess,
    handleSocialCallback,
    handleLogout
  }
} 