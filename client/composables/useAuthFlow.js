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
   * Handle manual logout (e.g., from UserDropdown)
   * Clears tokens, cache, and navigates to login page
   */
  const handleManualLogout = async () => {
    // Clear auth store tokens
    authStore.clearToken()
    
    // Clear user data
    authStore.user = null
    
    // Navigate to login page
    await router.push({ name: 'login' })
  }

  /**
   * Handle token expiry (401 errors)
   * Preserves cache and work state, opens QuickRegister modal
   */
  const handleTokenExpiry = async () => {
    const appStore = useAppStore()
    
    // Handle admin token expiry by undoing impersonation
    if (authStore.isImpersonating) {
      console.log("Admin token expired, undoing impersonation")
      authStore.stopImpersonating()
      useAlert().error("User token expired. You have been logged out of the admin account.")
      await router.push({ name: 'home' })
      return 
    }
    
    // Clear only the token, preserve user data and cache
    authStore.clearToken()
    
    // Set unauthorized error state and open quick login modal
    appStore.isUnauthorizedError = true
    appStore.quickLoginModal = true
  }

  return {
    // Auth flow functions
    handleAuthSuccess,
    handleSocialCallback,
    
    // Distinct logout methods
    handleManualLogout,
    handleTokenExpiry
  }
} 