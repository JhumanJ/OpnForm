import Vue from 'vue'
import axios from 'axios'

export const namespaced = true
export const workspaceEndpoint = '/api/open/workspaces/'

const localStorageCurrentWorkspaceKey = 'currentWorkspace'

// state
export const state = {
  content: [],
  currentId: null,
  loading: false
}

// getters
export const getters = {
  getById: (state) => (id) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.id === id)
  },
  getCurrent: (state) => () => {
    if (state.content.length === 0 || state.currentId === null) return null
    return state.content.find(item => item.id === state.currentId)
  }
}

// mutations
export const mutations = {
  set (state, items) {
    state.content = items
    if (state.currentId == null && state.content.length > 0) {
      // If one only, set it
      if (state.content.length === 1) {
        state.currentId = items[0].id
        localStorage.setItem(localStorageCurrentWorkspaceKey, state.currentId)
      } else if (localStorage.getItem(localStorageCurrentWorkspaceKey) && state.content.find(item => item.id === parseInt(localStorage.getItem(localStorageCurrentWorkspaceKey)))) {
        // Check local storage for current workspace, or take first
        state.currentId = parseInt(localStorage.getItem(localStorageCurrentWorkspaceKey))
        localStorage.setItem(localStorageCurrentWorkspaceKey, state.currentId)
      } else {
        // Else, take first
        state.currentId = items[0].id
        localStorage.setItem(localStorageCurrentWorkspaceKey, state.currentId)
      }
    } else {
      localStorage.removeItem(localStorageCurrentWorkspaceKey)
    }
  },
  setCurrentId (state, id) {
    state.currentId = id
    localStorage.setItem(localStorageCurrentWorkspaceKey, id)
  },
  addOrUpdate (state, item) {
    state.content = state.content.filter((val) => val.id !== item.id)
    state.content.push(item)
    if (state.currentId == null) {
      state.currentId = item.id
      localStorage.setItem(localStorageCurrentWorkspaceKey, state.currentId)
    }
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
  load (context) {
    context.commit('set', [])
    context.commit('startLoading')
    return axios.get(workspaceEndpoint).then((response) => {
      context.commit('set', response.data)
      context.commit('stopLoading')
    })
  },
  loadIfEmpty ({ context, dispatch, state }) {
    if (state.content.length === 0) {
      return dispatch('load')
    }
    return Promise.resolve()
  },
  delete ({ commit, dispatch, state }, id) {
    commit('startLoading')
    return axios.delete(workspaceEndpoint + id).then((response) => {
      commit('remove', response.data.workspace_id)
      commit('stopLoading')
    })
  }
}
