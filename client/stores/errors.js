import { defineStore } from 'pinia'

export const useErrorsStore = defineStore('errors', {
  state: () => ({
    content: null
  }),
  actions: {
    set (error) {
      this.content = error
    },
    clear () {
      this.content = null
    }
  }
})
