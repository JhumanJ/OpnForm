import { WindowMessageTypes, useWindowMessage } from "~/composables/useWindowMessage"

export const useAuth = () => {
  const authStore = useAuthStore()
  const workspaceStore = useWorkspacesStore()
  const formsStore = useFormsStore()
  const logEvent = useAmplitude().logEvent

  /**
   * Core authentication logic used by both social and direct login
   */
  const authenticateUser = async ({ tokenData, source, isNewUser = false }) => {
    // Set token first
    authStore.setToken(tokenData.token, tokenData.expires_in)

    // Fetch initial data
    const [userData, workspaces] = await Promise.all([
      opnFetch("user"),
      fetchAllWorkspaces()
    ])

    // Setup stores
    authStore.setUser(userData)
    workspaceStore.set(workspaces.data.value)
    
    // Load forms for current workspace
    await formsStore.loadAll(workspaceStore.currentId)

    // Track analytics
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

    return { userData, workspaces, isNewUser }
  }

  /**
   * Verify that authentication is complete and user data is loaded
   * Useful for social auth flows where token might be set but user data not loaded yet
   */
  const verifyAuthentication = async () => {
    // If we already have user data, no need to verify
    if (authStore.check) {
      return true
    }
    
    // If we have a token but no user data, fetch the user data
    if (authStore.token && !authStore.check) {
      // Create a promise with retry logic
      return new Promise((resolve, reject) => {
        const maxRetries = 3
        let retryCount = 0
        
        const attemptFetch = async () => {
          try {
            const userData = await opnFetch("user")
            
            if (userData) {
              authStore.setUser(userData)
              resolve(true)
            } else {
              handleRetry("No user data returned")
            }
          } catch (error) {
            handleRetry(`Auth verification failed: ${error.message}`)
          }
        }
        
        const handleRetry = (reason) => {
          retryCount++
          if (retryCount < maxRetries) {
            console.log(`Retrying auth verification (${retryCount}/${maxRetries}): ${reason}`)
            // Exponential backoff
            setTimeout(attemptFetch, 100 * Math.pow(2, retryCount))
          } else {
            console.error(`Auth verification failed after ${maxRetries} attempts`)
            reject(new Error(`Auth verification failed after ${maxRetries} attempts`))
          }
        }
        
        // Start the first attempt
        attemptFetch()
      })
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
    const tokenData = await opnFetch(`/oauth/${provider}/callback`, {
      method: 'POST',
      body: { code, utm_data: utmData }
    })
    
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

  return {
    loginWithCredentials,
    handleSocialCallback,
    registerUser,
    verifyAuthentication
  }
} 