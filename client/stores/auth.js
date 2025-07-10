import { defineStore } from "pinia"
import { authApi } from "~/api"

export const useAuthStore = defineStore("auth", {
  state: () => {
    return {
      token: null,
      admin_token: null,
      user: null,
    }
  },
  getters: {
    check: (state) => state.user !== null && state.user !== undefined,
    has_active_license: (state) => state.user !== null && state.user !== undefined && state.user.active_license !== null,
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

    setUser(user) {
      if (!user) {
        console.error("No user, logging out.")
        // When logging out due to no user, clear the token with maxAge 0
        this.setToken(null, 0)
      }

      this.user = user
      this.initServiceClients()
    },

    updateUser(payload) {
      this.user = payload
      this.initServiceClients()
    },

    initServiceClients() {
      if (!this.user) return
      useAmplitude().setUser(this.user)
      useCrisp().setUser(this.user)

      // todo: set sentry user
    },

    logout() {
      authApi.logout().catch(() => {})

      this.user = null
      
      // Clear the token cookie by setting maxAge to 0
      this.setCookie("token", null, { maxAge: 0 })
      this.token = null
    },

    // async fetchOauthUrl() {
      // const {data} = await axios.post(`/api/oauth/${provider}`)
      // return data.url
    // },
  },
})
