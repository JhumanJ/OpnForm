import axios from 'axios'

export const namespaced = true
export const workspaceEndpoint = '/api/open/records/'

/**
 * Loads records from database
 */

// state
export const state = {
  content: [],
  loading: false
}

// getters
export const getters = {
  getById: (state) => (id) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.submission_id === id)
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
  remove (state, itemId) {
    state.content = state.content.filter((val) => val.id !== itemId)
  },
  startLoading () {
    state.loading = true
  },
  stopLoading () {
    state.loading = false
  }
}

// actions
export const actions = {
  resetState (context) {
    context.commit('set', [])
    context.commit('stopLoading')
  },
  loadRecord (context, request) {
    context.commit('set', [])
    context.commit('startLoading')
    return request.then((data) => {
      context.commit('addOrUpdate', data)
      context.commit('stopLoading')
    })
  }
}