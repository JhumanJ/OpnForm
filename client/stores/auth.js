import {defineStore} from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => {
    return {
      token: null,
      admin_token: null,
      user: null,
    }
  },
  getters: {
    check: (state) => (state.user !== null && state.user !== undefined),
    isImpersonating: (state) => (state.admin_token !== null && state.admin_token !== undefined)
  },
  actions: {
    // Stores admin token temporarily for impersonation
    startImpersonating() {
      this.setAdminToken(this.token)
    },
    // Stop admin impersonation
    stopImpersonating() {
      this.token = this.admin_token
      this.admin_token = null
      this.fetchUser()
    },

    setToken(token) {
      useCookie('token', {maxAge: 60 * 60 * 24 * 30}).value = token
      this.token = token
    },

    setAdminToken(token) {
      useCookie('admin_token', {maxAge: 60 * 60 * 24 * 30}).value = token
      this.admin_token = token
    },

    loadTokenFromCookie() {
      this.token = useCookie('token').value
      this.admin_token = useCookie('admin_token').value
    },

    async fetchUser() {
      try {
        const {data} = await axios.get('/api/user')
        this.user = data
        this.initServiceClients()

        return data
      } catch (e) {
        this.setToken(null)
      }
    },

    async fetchUserIfNotFetched() {
      if (this.user === null && this.token) {
        await this.fetchUser()
      }
    },

    updateUser(payload) {
      this.user = payload
      this.initServiceClients()
    },

    initServiceClients() {
      if (!this.user) return
      useAmplitude().setUser(this.user)
      useCrisp().setUser(this.user)

      // Init sentry
      Sentry.configureScope((scope) => {
        scope.setUser({
          id: this.user.id,
          email: this.user.email,
          subscription: this.user?.is_subscribed
        })
      })
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (e) {
      }

      this.user = null
      this.setToken(null)
    },

    async fetchOauthUrl(provider) {
      const {data} = await axios.post(`/api/oauth/${provider}`)
      return data.url
    }
  }
})
