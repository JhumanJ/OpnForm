export const namespaced = true

// state
export const state = {
  content: null,

  // Field being edited
  selectedFieldIndex: null,
  showEditFieldModal: null
}

// mutations
export const mutations = {
  set (state, form) {
    state.content = form
  },
  setProperties (state, properties) {
    state.content.properties = properties
  },
  openSettingsForField (state, index) {
    // If field is passed, compute index
    if (typeof index === 'object') {
      index = state.content.properties.findIndex(prop => prop.id === index.id)
    }
    state.selectedFieldIndex = index
    state.showEditFieldModal = true
  },
  setSelectedFieldIndex (state, index) {
    state.selectedFieldIndex = index
  },
  openEditFieldModal (state) {
    state.showEditFieldModal = true
  },
  closeEditFieldModal (state) {
    state.showEditFieldModal = false
    this.selectedFieldIndex = null
  }
}
