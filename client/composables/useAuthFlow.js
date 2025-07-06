import { WindowMessageTypes, useWindowMessage } from "~/composables/useWindowMessage"
import { authApi } from "~/api"

export const useAuthFlow = () => {
  const authStore = useAuthStore()
  const formsStore = useFormsStore()
  const logEvent = useAmplitude().logEvent
  const { list: fetchWorkspaces } = useWorkspaces()
  const { user, invalidateUser } = useAuth()

  // Computed properties moved from auth store
  const userData = computed(() => {
    const userQuery = user()
    return userQuery.data.value || null
  })

  const isAuthenticated = computed(() => {
    return userData.value !== null && userData.value !== undefined
  })

  const hasActiveLicense = computed(() => {
    const userVal = userData.value
    return userVal !== null && userVal !== undefined && userVal.active_license !== null
  })

  // Service client initialization moved from auth store
  const initServiceClients = (userDataOverride = null) => {
    if (import.meta.server) return
    
    const userVal = userDataOverride || userData.value
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

    // 2. Fetch workspaces and trigger user query
    const [workspacesResult] = await Promise.all([
      fetchWorkspaces(),
      // Invalidate user query to trigger fresh fetch with new token
      invalidateUser()
    ])
    const workspaces = workspacesResult

    // 3. Wait for user data to be fetched by TanStack Query
    // The user query will automatically cache and trigger onSuccess
    const userQuery = user()
    await userQuery.suspense()
    
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
    // If we have user data in cache, we're good
    if (isAuthenticated.value) {
      return true
    }
    
    // If we have a token but no user data, fetch the user data
    if (authStore.token && !isAuthenticated.value) {
      try {
        const userQuery = user()
        await userQuery.suspense()
        return true
      } catch (error) {
        console.error('Auth verification failed:', error)
        return false
      }
    }
    
    return false
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
   * Coordinates between TanStack Query mutation and store cleanup
   */
  const handleLogout = async () => {
    const { logout } = useAuth()
    const logoutMutation = logout()
    
    try {
      await logoutMutation.mutateAsync()
    } catch (error) {
      // Even if API call fails, we still want to clear local state
      console.warn('Logout API call failed, but clearing local state anyway:', error)
    }
    
    // Additional cleanup for forms (workspace data is handled by TanStack Query cache clearing)
    formsStore.set([])
    
    // Navigate to login page
    useRouter().push({ name: 'login' })
  }

  return {
    // Auth flow functions
    loginWithCredentials,
    handleSocialCallback,
    registerUser,
    verifyAuthentication,
    handleLogout,
    
    // Computed properties (moved from auth store)
    userData,
    isAuthenticated,
    hasActiveLicense,
    
    // Helper functions
    initServiceClients
  }
} 