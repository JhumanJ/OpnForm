export const namespaced = true

// state
export const state = {
  content: null,

  // Field being edited
  selectedFieldIndex: null,
  showEditFieldSidebar: null,
  showAddFieldModal: null
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
    state.showEditFieldSidebar = true
    state.showAddFieldModal = false
  },   
  closeEditFieldSidebar (state) {
    state.selectedFieldIndex = null
    state.showEditFieldSidebar = false
    state.showAddFieldModal = false
  },
  openAddFieldModal (state, index) {
    // If field is passed, compute index
    if (index !== null && typeof index === 'object') {
      index = state.content.properties.findIndex(prop => prop.id === index.id)
    }
    state.selectedFieldIndex = index
    state.showAddFieldModal = true
    state.showEditFieldSidebar = false
  },
  closeAddFieldModal (state) {
    state.selectedFieldIndex = null
    state.showAddFieldModal = false
    state.showEditFieldSidebar = false
  },
}
