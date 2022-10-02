import Vue from 'vue'
import axios from 'axios'

// state
export const state = {
  content: [],
  loading: false
}

// getters
export const getters = {
  getById: (state) => (id) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.id === id)
  },
  getBySlug: (state) => (slug) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.slug === slug)
  },
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
  startLoading () {
    state.loading = true
  },
  stopLoading () {
    state.loading = false
  }
}

// actions
export const actions = {
  load (context) {
    context.commit('set', [])
    context.commit('startLoading')
    return axios.get('/api/templates').then((response) => {
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
}
