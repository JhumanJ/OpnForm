import axios from 'axios'

export const formsEndpoint = '/api/open/workspaces/{workspaceId}/forms'
export const namespaced = true

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
  getAllTags: (state) => {
    if (state.content.length === 0) return []
    let allTags = []
    state.content.forEach(form => {
      if(form.tags && form.tags.length > 0){
        allTags = allTags.concat(form.tags)
      }
    })
    return allTags.filter((item, i, ar) => ar.indexOf(item) === i)
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
  },
  startLoading (state) {
    state.loading = true
  },
  stopLoading (state) {
    state.loading = false
  }
}

// actions
export const actions = {
  resetState (context) {
    context.commit('set', [])
    context.commit('stopLoading')
  },
  load (context, workspaceId) {
    context.commit('startLoading')
    return axios.get(formsEndpoint.replace('{workspaceId}', workspaceId)).then((response) => {
      context.commit('set', response.data)
      context.commit('stopLoading')
    })
  }
}
