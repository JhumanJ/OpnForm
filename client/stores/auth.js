import { defineStore } from 'pinia'
import axios from 'axios'
import Cookies from 'js-cookie'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: Cookies.get('token'),

    // For admin impersonation
    admin_token: Cookies.get('admin_token') ?? null
  }),
  getters: {
    check: (state) => (state.user !== null && state.user !== undefined),
    isImpersonating: (state) => (state.admin_token !== null && state.admin_token !== undefined)
  },
  actions: {
    // Stores admin token temporarily for impersonation
    startImpersonating () {
      this.admin_token = this.token
      Cookies.set('admin_token', this.token, { expires: 365 })
    },
    // Stop admin impersonation
    stopImpersonating () {
      this.token = this.admin_token
      this.admin_token = null
      Cookies.set('token', this.token, { expires: 365 })
      Cookies.remove('admin_token')
      this.fetchUser()
    },

    saveToken (token, remember) {
      this.token = token
      Cookies.set('token', token, { expires: remember ? 365 : null })
    },

    async fetchUser () {
      try {
        const { data } = await axios.get('/api/user')
        this.user = data
        return data
      } catch (e) {
        this.token = null
        Cookies.remove('token')
      }
    },
  
    updateUser (payload) {
      this.user = payload
    },
  
    async logout () {
      try {
        await axios.post('/api/logout')
      } catch (e) { }
  
      this.user = null
      this.token = null
      Cookies.remove('token')
    },
  
    async fetchOauthUrl (provider) {
      const { data } = await axios.post(`/api/oauth/${provider}`)
      return data.url
    }
  }
})