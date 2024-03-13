import { defineStore } from 'pinia'

export const useWorkingFormStore = defineStore('working_form', {
  state: () => ({
    content: null,

    // Field being edited
    selectedFieldIndex: null,
    showEditFieldSidebar: null,
    showAddFieldSidebar: null
  }),
  actions: {
    set (form) {
      this.content = form
    },
    setProperties (properties) {
      this.content.properties = [...properties]
    },
    openSettingsForField (index) {
      // If field is passed, compute index
      if (typeof index === 'object') {
        index = this.content.properties.findIndex(prop => prop.id === index.id)
      }
      this.selectedFieldIndex = index
      this.showEditFieldSidebar = true
      this.showAddFieldSidebar = false
    },
    closeEditFieldSidebar () {
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = false
      this.showAddFieldSidebar = false
    },
    openAddFieldSidebar (index) {
      // If field is passed, compute index
      if (index !== null && typeof index === 'object') {
        index = this.content.properties.findIndex(prop => prop.id === index.id)
      }
      this.selectedFieldIndex = index
      this.showAddFieldSidebar = true
      this.showEditFieldSidebar = false
    },
    closeAddFieldSidebar () {
      this.selectedFieldIndex = null
      this.showAddFieldSidebar = false
      this.showEditFieldSidebar = false
    },
    reset () {
      this.content = null
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = null
      this.showAddFieldSidebar = null
    }
  }
})
