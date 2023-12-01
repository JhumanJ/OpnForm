import { defineStore } from 'pinia'

export const namespaced = true

/**
 * Loads records from database
 */
export const useRecordsStore = defineStore('records', {
  state: () => ({
    content: [],
    loading: false
  }),
  getters: {
    getById: (state) => (id) => {
      if (state.content.length === 0) return null
      return state.content.find(item => item.submission_id === id)
    }
  },
  actions: {
    set (items) {
      this.content = items
    },
    addOrUpdate (item) {
      this.content = this.content.filter((val) => val.id !== item.id)
      this.content.push(item)
    },
    remove (itemId) {
      this.content = this.content.filter((val) => val.id !== itemId)
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
    loadRecord (request) {
      this.set([])
      this.startLoading()
      return request.then((data) => {
        this.addOrUpdate(data)
        this.stopLoading()
      })
    }
  }
})