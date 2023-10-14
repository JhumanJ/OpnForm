import Vue from 'vue'

export const namespaced = true

// state
export const state = {
  content: null
}

// getters
export const getters = {
}

// mutations
export const mutations = {
  set (state, error) {
    state.content = error
  },
  clear (state) {
    state.content = null
  }
}

// actions
export const actions = {

}
