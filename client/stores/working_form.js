import { defineStore } from "pinia"
import clonedeep from "clone-deep"
import { generateUUID } from "~/lib/utils.js"
import blocksTypes from "~/data/blocks_types.json"
import { useAlert } from '~/composables/useAlert'

export const useWorkingFormStore = defineStore("working_form", {
  state: () => ({
    content: null,
    activeTab: 0,
    formPageIndex: 0,

    // Field being edited
    selectedFieldIndex: null,
    showEditFieldSidebar: null,
    showAddFieldSidebar: null,
    blockForm: null,
    draggingNewBlock: false,
  }),
  actions: {
    set(form) {
      this.content = form
    },
    setProperties(properties) {
      this.content.properties = [...properties]
    },
    objectToIndex(field) {
      if (typeof field === 'object') {
        return this.content.properties.findIndex(
          prop => prop.id === field.id
        )
      }
      return field
    },
    setEditingField(field) {
      this.selectedFieldIndex = this.objectToIndex(field)
    },
    openSettingsForField(field) {
      this.setEditingField(field)
      this.showEditFieldSidebar = true
      this.showAddFieldSidebar = false
    },
    closeEditFieldSidebar() {
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = false
      this.showAddFieldSidebar = false
    },
    openAddFieldSidebar(field) {
      if (field !== null) {
        this.setEditingField(field)
      }
      this.showAddFieldSidebar = true
      this.showEditFieldSidebar = false
    },
    closeAddFieldSidebar() {
      this.selectedFieldIndex = null
      this.showAddFieldSidebar = false
      this.showEditFieldSidebar = false
    },
    reset() {
      this.content = null
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = null
      this.showAddFieldSidebar = null
    },

    resetBlockForm() {
      this.blockForm = useForm({
        type: null,
        name: null,
      })

    },

    prefillDefault(data) {
      // If a field already has this name, we need to make it unique with a number at the end
      let baseName = data.name
      let counter = 1
      while (this.content.properties.some(prop => prop.name === data.name)) {
        counter++
        data.name = `${baseName} ${counter}`
      }
      
      if (data.type === "nf-text") {
        data.content = "<p>This is a text block.</p>"
      } else if (data.type === "nf-page-break") {
        data.next_btn_text = "Next"
        data.previous_btn_text = "Previous"
      } else if (data.type === "nf-code") {
        data.content =
          '<div class="text-blue-500 italic">This is a code block.</div>'
      } else if (data.type === "signature") {
        data.help = "Draw your signature above"
      }
      return data
    },

    addBlock(type, index = null, openSettings = true) {
      this.blockForm.type = type
      this.blockForm.name = blocksTypes[type].default_block_name
      const newBlock = this.prefillDefault(this.blockForm.data())
      newBlock.id = generateUUID()
      newBlock.hidden = false
      if (["select", "multi_select"].includes(this.blockForm.type)) {
        newBlock[this.blockForm.type] = { options: [] }
      }
      if (this.blockForm.type === "rating") {
        newBlock.rating_max_value = 5
      }
      if (this.blockForm.type === "scale") {
        newBlock.scale_min_value = 1
        newBlock.scale_max_value = 5
        newBlock.scale_step_value = 1
      }
      if (this.blockForm.type === "slider") {
        newBlock.slider_min_value = 0
        newBlock.slider_max_value = 50
        newBlock.slider_step_value = 1
      }
      newBlock.help_position = "below_input"
      if (
        (this.selectedFieldIndex === null || this.selectedFieldIndex === undefined) &&
        (index === null || index === undefined)
      ) {
        const newFields = clonedeep(this.content.properties)
        newFields.push(newBlock)
        this.content.properties = newFields
        if (openSettings) {
          this.openSettingsForField(
            this.content.properties.length - 1,
          )
        }
      } else {
        const fieldIndex = typeof index === "number" ? index : this.selectedFieldIndex + 1
        const newFields = clonedeep(this.content.properties)
        newFields.splice(fieldIndex, 0, newBlock)
        this.content.properties = newFields
        if (openSettings) {
          this.openSettingsForField(fieldIndex)
        }
      }
    },
    removeField(field) {
      this.internalRemoveField(field)
    },
    internalRemoveField(field) {
      const index = this.objectToIndex(field)

      if (index !== -1) {
        useAlert().success('Ctrl + Z to undo',10000,{
          title: 'Field removed',
          actions: [{
            label: 'Undo',
            icon:"i-material-symbols-undo",
            click: () => {
              this.undo()
            }
          }]
        })
        this.content.properties.splice(index, 1)
      }
    },
    removeField(field) {
      this.internalRemoveField(field)
    },
    internalRemoveField(field) {
      const index = this.objectToIndex(field)

      if (index !== -1) {
        useAlert().success('Ctrl + Z to undo',10000,{
          title: 'Field removed',
          actions: [{
            label: 'Undo',
            icon:"i-material-symbols-undo",
            click: () => {
              this.undo()
            }
          }]
        })
        this.content.properties.splice(index, 1)
      }
    },

    moveField(oldIndex, newIndex) {
      const newFields = clonedeep(this.content.properties)
      const field = newFields.splice(oldIndex, 1)[0]
      newFields.splice(newIndex, 0, field)
      this.content.properties = newFields
    }
  },
  history: {}
})
