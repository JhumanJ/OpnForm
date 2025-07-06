import { defineStore } from "pinia"

export const useAuthStore = defineStore("auth", {
  state: () => {
    return {
      token: null,
      admin_token: null,
    }
  },
  getters: {
    isImpersonating: (state) =>
      state.admin_token !== null && state.admin_token !== undefined,
  },
  actions: {
    // Stores admin token temporarily for impersonation
    startImpersonating() {
      this.setAdminToken(this.token)
    },
    
    // Stop admin impersonation
    stopImpersonating() {
      // When stopping impersonation, we don't have expiration info for the admin token
      // Use a default long expiration (24 hours) to ensure the admin can continue working
      this.setToken(this.admin_token, 60 * 60 * 24)
      this.setAdminToken(null)
      
      // Invalidate user query to refetch admin user data
      if (import.meta.client) {
        const { invalidateUser } = useAuth()
        invalidateUser()
      }
    },

    setToken(token, expiresIn) {
      // Set cookie with expiration if provided
      const cookieOptions = {}
      
      if (expiresIn) {
        // expiresIn is in seconds, maxAge also needs to be in seconds
        cookieOptions.maxAge = expiresIn
      }
      
      this.setCookie("token", token, cookieOptions)
      this.token = token
    },

    setAdminToken(token) {
      this.setCookie("admin_token", token)
      this.admin_token = token
    },

    setCookie(name, value, options = {}) {
      if (import.meta.client) {
        useCookie(name, options).value = value
      }
    },

    initStore(token, adminToken) {
      this.token = token
      this.admin_token = adminToken
    },

    // Clear tokens and coordinate with TanStack Query
    clearTokens() {
      this.token = null
      this.admin_token = null
      
      // Clear cookies
      this.setCookie("token", null, { maxAge: 0 })
      this.setCookie("admin_token", null, { maxAge: 0 })
    },

    logout() {
      // The actual logout API call should be handled by TanStack Query mutation
      // This just clears local state
      this.clearTokens()
    },
  },
})
