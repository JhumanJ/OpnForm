import axios from 'axios'

export const templatesEndpoint = '/api/templates'
export const namespaced = true

// state
export const state = {
  content: [],
  industries: {},
  types: {},
  allLoaded: false,
  loading: false
}

// getters
export const getters = {
  getBySlug: (state) => (slug) => {
    if (state.content.length === 0) return null
    return state.content.find(item => item.slug === slug)
  },
  getTemplateTypes: (state) => (slugs) => {
    if (state.types.length === 0) return null
    return Object.values(state.types).filter((val) => slugs.includes(val.slug)).map((item) => { return item.name })
  },
  getTemplateIndustries: (state) => (slugs) => {
    if (state.industries.length === 0) return null
    return Object.values(state.industries).filter((val) => slugs.includes(val.slug)).map((item) => { return item.name })
  }
}

// mutations
export const mutations = {
  set (state, items) {
    state.content = items
    state.allLoaded = true
  },
  append (state, items) {
    const ids = items.map((item) => { return item.id })
    state.content = state.content.filter((val) => !ids.includes(val.id))
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
  },
  setAllLoaded (state, val) {
    state.allLoaded = val
  }
}

// actions
export const actions = {
  resetState (context) {
    context.commit('set', [])
    context.commit('stopLoading')
  },
  loadTypesAndIndustries (context) {
    if (Object.keys(context.state.industries).length === 0) {
      import('@/data/forms/templates/industries.json').then((module) => {
        context.state.industries = module.default
      })
    }
    if (Object.keys(context.state.types).length === 0) {
      import('@/data/forms/templates/types.json').then((module) => {
        context.state.types = module.default
      })
    }
  },
  loadTemplate (context, slug) {
    context.commit('startLoading')
    context.dispatch('loadTypesAndIndustries')

    if (context.getters.getBySlug(slug)) {
      context.commit('stopLoading')
      return Promise.resolve()
    }

    return axios.get(templatesEndpoint + '/' + slug).then((response) => {
      context.commit('addOrUpdate', response.data)
      context.commit('stopLoading')
    }).catch((error) => {
      context.commit('stopLoading')
    })
  },
  loadAll (context) {
    context.commit('startLoading')
    context.dispatch('loadTypesAndIndustries')
    return axios.get(templatesEndpoint).then((response) => {
      context.commit('append', response.data)
      context.commit('setAllLoaded', true)
      context.commit('stopLoading')
    }).catch((error) => {
      context.commit('stopLoading')
    })
  },
  loadIfEmpty (context) {
    if (!context.state.allLoaded) {
      return context.dispatch('loadAll')
    }
    context.commit('stopLoading')
    return Promise.resolve()
  },
  loadWithLimit (context, limit) {
    context.commit('startLoading')
    context.dispatch('loadTypesAndIndustries')

    return axios.get(templatesEndpoint + '?limit=' + limit).then((response) => {
      context.commit('set', response.data)
      context.commit('setAllLoaded', false)
      context.commit('stopLoading')
    }).catch((error) => {
      context.commit('stopLoading')
    })
  }
}
