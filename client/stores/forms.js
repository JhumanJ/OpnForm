import {defineStore} from 'pinia'
import {useOpnFetch} from "~/composables/useOpnFetch.js";

export const formsEndpoint = '/open/workspaces/{workspaceId}/forms'
export let currentPage = 1

export const useFormsStore = defineStore('forms', {
  state: () => ({
    content: [],
    loading: false
  }),
  getters: {
    getById: (state) => (id) => {
      if (state.content.length === 0) return null
      return state.content.find(item => item.id === id)
    },
    getBySlug: (state) => (slug) => {
      if (state.content.length === 0) return null
      return state.content.find(item => item.slug === slug)
    },
    getAllTags: (state) => {
      if (state.content.length === 0) return []
      let allTags = []
      state.content.forEach(form => {
        if (form.tags && form.tags.length > 0) {
          allTags = allTags.concat(form.tags)
        }
      })
      return allTags.filter((item, i, ar) => ar.indexOf(item) === i)
    }
  },
  actions: {
    set(items) {
      this.content = items
    },
    append(items) {
      this.content = this.content.concat(items)
    },
    addOrUpdate(item) {
      this.content = this.content.filter((val) => val.id !== item.id)
      this.content.push(item)
    },
    remove(item) {
      this.content = this.content.filter((val) => val.id !== item.id)
    },
    startLoading() {
      this.loading = true
    },
    stopLoading() {
      this.loading = false
    },
    resetState() {
      this.set([])
      this.stopLoading()
      currentPage = 1
    },
    load(workspaceId) {
      this.startLoading()
      return useOpnFetch(formsEndpoint.replace('{workspaceId}', workspaceId) + '?page=' + currentPage).get().then((response) => {
        if (currentPage === 1) {
          this.set(response.data.data)
        } else {
          this.append(response.data.data)
        }
        if (currentPage < response.data.meta.last_page) {
          currentPage += 1
          this.load(workspaceId)
        } else {
          this.stopLoading()
          currentPage = 1
        }
      })
    },
    loadIfEmpty(workspaceId) {
      if (this.content.length === 0) {
        return this.load(workspaceId)
      }
      this.stopLoading()
      return Promise.resolve()
    }
  }
})
