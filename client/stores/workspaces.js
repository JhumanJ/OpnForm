import axios from 'axios'
import { defineStore } from 'pinia'
export const workspaceEndpoint = '/api/open/workspaces/'

const localStorageCurrentWorkspaceKey = 'currentWorkspace'

export const useWorkspacesStore = defineStore('workspaces', {
  state: () => ({
    content: [],
    currentId: null,
    loading: false
  }),
  getters: {
    getById: (state) => (id) => {
      if (state.content.length === 0) return null
      return state.content.find(item => item.id === id)
    },
    getCurrent: (state) => () => {
      if (state.content.length === 0 || state.currentId === null) return null
      return state.content.find(item => item.id === state.currentId)
    }
  },
  actions: {
    set (items) {
      this.content = items
      if (this.currentId == null && this.content.length > 0) {
        // If one only, set it
        if (this.content.length === 1) {
          this.currentId = items[0].id
          localStorage.setItem(localStorageCurrentWorkspaceKey, this.currentId)
        } else if (localStorage.getItem(localStorageCurrentWorkspaceKey) && this.content.find(item => item.id === parseInt(localStorage.getItem(localStorageCurrentWorkspaceKey)))) {
          // Check local storage for current workspace, or take first
          this.currentId = parseInt(localStorage.getItem(localStorageCurrentWorkspaceKey))
          localStorage.setItem(localStorageCurrentWorkspaceKey, this.currentId)
        } else {
          // Else, take first
          this.currentId = items[0].id
          localStorage.setItem(localStorageCurrentWorkspaceKey, this.currentId)
        }
      } else {
        localStorage.removeItem(localStorageCurrentWorkspaceKey)
      }
    },
    setCurrentId (id) {
      this.currentId = id
      localStorage.setItem(localStorageCurrentWorkspaceKey, id)
    },
    addOrUpdate (item) {
      this.content = this.content.filter((val) => val.id !== item.id)
      this.content.push(item)
      if (this.currentId == null) {
        this.currentId = item.id
        localStorage.setItem(localStorageCurrentWorkspaceKey, this.currentId)
      }
    },
    remove (itemId) {
      this.content = this.content.filter((val) => val.id !== itemId)
      if (this.currentId === itemId) {
        this.currentId = this.content.length > 0 ? this.content[0].id : null
        localStorage.setItem(localStorageCurrentWorkspaceKey, this.currentId)
      }
    },
    startLoading () {
      this.loading = true
    },
    stopLoading () {
      this.loading = false
    },
    resetState () {
      this.set([])
      this.stopLoading()
    },
    load () {
      this.set([])
      this.startLoading()
      return axios.get(workspaceEndpoint).then((response) => {
        this.set(response.data)
        this.stopLoading()
      })
    },
    loadIfEmpty () {
      if (this.content.length === 0) {
        return this.load()
      }
      return Promise.resolve()
    },
    delete (id) {
      this.startLoading()
      return axios.delete(workspaceEndpoint + id).then((response) => {
        this.remove(response.data.workspace_id)
        this.stopLoading()
      })
    }
  }
})
