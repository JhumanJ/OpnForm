export const namespaced = true

// state
export const state = {
  content: null,

  // Field being edited
  selectedFieldIndex: null,
  showEditFieldSidebar: null,
  showAddFieldSidebar: null
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
    state.showAddFieldSidebar = false
  },   
  closeEditFieldSidebar (state) {
    state.selectedFieldIndex = null
    state.showEditFieldSidebar = false
    state.showAddFieldSidebar = false
  },
  openAddFieldSidebar (state, index) {
    // If field is passed, compute index
    if (index !== null && typeof index === 'object') {
      index = state.content.properties.findIndex(prop => prop.id === index.id)
    }
    state.selectedFieldIndex = index
    state.showAddFieldSidebar = true
    state.showEditFieldSidebar = false
  },
  closeAddFieldSidebar (state) {
    state.selectedFieldIndex = null
    state.showAddFieldSidebar = false
    state.showEditFieldSidebar = false
  },
}
