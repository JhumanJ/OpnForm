import Vue from 'vue'

export const namespaced = true

// state
export const state = {
  content: []
}

// getters
export const getters = {
  getById: (state) => (id) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.id === id)
  }
}

// mutations
export const mutations = {
  set (state, items) {
    state.content = items
  },
  addOrUpdate (state, item) {
    state.content = state.content.filter((val) => val.id !== item.id)
    state.content.push(item)
  },
  remove (state, item) {
    state.content = state.content.filter((val) => val.id !== item.id)
  }
}

// actions
export const actions = {

}
