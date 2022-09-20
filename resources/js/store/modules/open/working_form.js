export const namespaced = true

// state
export const state = {
  content: null
}

// mutations
export const mutations = {
  set (state, form) {
    state.content = form
  }
}
