export const useAuth = () => {
  const authStore = useAuthStore()
  const workspaceStore = useWorkspacesStore()
  const formsStore = useFormsStore()
  const logEvent = useAmplitude().logEvent

  /**
   * Core authentication logic used by both social and direct login
   */
  const authenticateUser = async ({ token, source, isNewUser = false }) => {
    // Set token first
    authStore.setToken(token)

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
      useGtm().trackEvent({
        event: eventName,
        source
      })
    } catch (error) {
      console.error(error)
    }

    return { userData, workspaces, isNewUser }
  }

  /**
   * Handle direct login with form validation
   */
  const loginWithCredentials = async (form) => {
    const { token } = await form.submit('post', '/login')
    
    return authenticateUser({ 
      token, 
      source: 'credentials'
    })
  }

  /**
   * Handle social login callback
   */
  const handleSocialCallback = async (provider, code, utmData) => {
    const { token, new_user } = await opnFetch(`/oauth/${provider}/callback`, {
      method: 'POST',
      body: { code, utm_data: utmData }
    })

    return authenticateUser({ 
      token, 
      source: provider,
      isNewUser: new_user
    })
  }

  /**
   * Handle user registration
   */
  const registerUser = async (form) => {
    // Register the user first
    const data = await form.submit('post', '/register')
    
    // Login the user
    const { token } = await form.submit('post', '/login')
    
    const result = await authenticateUser({ 
      token, 
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
    registerUser
  }
} 