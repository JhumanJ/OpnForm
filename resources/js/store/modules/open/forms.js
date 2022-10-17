import axios from 'axios'

export const formsEndpoint = '/api/open/workspaces/{workspaceId}/forms'
export const namespaced = true
export let currentPage = 1

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
  append (state, items) {
    state.content = state.content.concat(items)
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
    currentPage = 1
  },
  load (context, workspaceId) {
    context.commit('startLoading')
    return axios.get(formsEndpoint.replace('{workspaceId}', workspaceId)+'?page='+currentPage).then((response) => {
      context.commit((currentPage == 1) ? 'set' : 'append', response.data.data)
      if (currentPage < response.data.meta.last_page) {
        currentPage += 1
        context.dispatch('load', workspaceId)
      } else {
        context.commit('stopLoading')
        currentPage = 1
      }
    })
  },
  loadIfEmpty (context, workspaceId) {
    if (context.state.content.length === 0) {
      return context.dispatch('load', workspaceId)
    }
    context.commit('stopLoading')
    return Promise.resolve()
  }
}
