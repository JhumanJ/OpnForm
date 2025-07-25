import { defineStore } from "pinia"
import clonedeep from "clone-deep"
import { generateUUID } from "~/lib/utils.js"
import blocksTypes from "~/data/blocks_types.json"
import { useAlert } from '~/composables/useAlert'
import { useIsAuthenticated } from '~/composables/useAuthFlow'

import { useForm } from '~/composables/useForm'

export const useWorkingFormStore = defineStore("working_form", {
  state: () => ({
    content: null,
    activeTab: 'build',
    
    // Field being edited
    selectedFieldIndex: null,
    showEditFieldSidebar: null,
    showAddFieldSidebar: null,
    blockForm: null,
    draggingNewBlock: false,
    
    // Structure service instance - will be set from useFormManager
    structureService: null,
  }),
  getters: {
    // Get all blocks/properties in the form
    formBlocks() {
      return this.content?.properties || []
    },

    // Get page count using structure service
    simplePageCount() {
      if (!this.structureService) return 1
      return this.structureService.pageCount.value
    },
    
    // Current page index from structure service
    formPageIndex() {
      if (!this.structureService) return 0
      return this.structureService.currentPage
    }
  },
  actions: {
    set(form) {
      this.content = form
      // Don't reset structure service here - it's externally managed now
    },
    setStructureService(service) {
      this.structureService = service
    },
    setProperties(properties) {
      if (!this.content) return
      this.content.properties = [...properties]
      // No need to reset structure service as it's externally managed
    },
    objectToIndex(field) {
      if (!this.content?.properties) return -1
      if (typeof field === 'object' && field !== null && field.id !== undefined) {
        return this.content.properties.findIndex(
          prop => prop && prop.id === field.id
        )
      }
      if (typeof field === 'number') {
        return field
      }
      return -1
    },
    setEditingField(field) {
      this.selectedFieldIndex = this.objectToIndex(field)
    },
    openSettingsForField(field) {
      const targetIndex = this.objectToIndex(field)
      const previousIndex = this.selectedFieldIndex
      
      this.selectedFieldIndex = targetIndex
      this.showEditFieldSidebar = true
      this.showAddFieldSidebar = false
      
      if (this.selectedFieldIndex !== -1 && previousIndex !== this.selectedFieldIndex) {
        // Find which page contains the selected field and set to that page
        if (this.structureService) {
          this.structureService.setPageForField(this.selectedFieldIndex)
        }
      }
    },
    closeEditFieldSidebar() {
      this.showEditFieldSidebar = false
    },
    openAddFieldSidebar(field = null) {
      if (field !== null) {
        this.setEditingField(field)
      } else {
        this.selectedFieldIndex = null
      }
      this.showAddFieldSidebar = true
      this.showEditFieldSidebar = false
    },
    closeAddFieldSidebar() {
      this.selectedFieldIndex = null
      this.showAddFieldSidebar = false
    },
    reset() {
      this.content = null
      this.selectedFieldIndex = null
      this.showEditFieldSidebar = false
      this.showAddFieldSidebar = false
      this.blockForm = null
      this.structureService = null
    },

    resetBlockForm() {
      this.blockForm = useForm({
        type: null,
        name: null,
      })
    },

    /**
     * Determine where to insert a new block based on current page index and selection.
     * Uses the FormStructureService if available.
     * @param {number|null} explicitIndex - Optional explicit index to insert at
     * @returns {number} The index where the block should be inserted relative to all properties
     */
    determineInsertIndex(explicitIndex, insertOnSamePage = false) {
      // If we have a structure service, use its method
      if (this.structureService) {
        return this.structureService.determineInsertIndex(
          this.selectedFieldIndex,
          this.formPageIndex,
          explicitIndex,
          insertOnSamePage
        )
      }
      
      // Fallback to old logic
      if (explicitIndex !== null && typeof explicitIndex === 'number') {
        return explicitIndex
      }

      if (this.selectedFieldIndex !== null && this.selectedFieldIndex >= 0) {
        return this.selectedFieldIndex + 1
      }
      
      // Default: end of properties array
      return this.content?.properties?.length || 0
    },

    prefillDefault(data) {
      if (!this.content?.properties) return data

      let baseName = data.name
      let counter = 1
      let uniqueName = data.name
      while (this.content.properties.some(prop => prop && prop.name === uniqueName)) {
        counter++
        uniqueName = `${baseName} ${counter}`
      }
      data.name = uniqueName
      
      if (data.type === "nf-text") {
        data.content = "<p>This is a text block.</p>"
      } else if (data.type === "nf-page-break") {
        data.next_btn_text = null
        data.previous_btn_text = null
      } else if (data.type === "nf-code") {
        data.content =
          '<div class="text-blue-500 italic">This is a code block.</div>'
      } else if (data.type === "signature") {
        data.help = "Draw your signature above"
      }
      return data
    },

    addBlock(type, index = null, openSettings = true) {
      if (!this.blockForm) {
        this.resetBlockForm()
      }
      if (!this.content) return
      
      const originalBlockDefinition = blocksTypes[type]
      const effectiveType = originalBlockDefinition?.actual_input || type
      const effectiveBlockDefinition = blocksTypes[effectiveType]

      if (originalBlockDefinition?.self_hosted !== undefined && !originalBlockDefinition.self_hosted && useFeatureFlag('self_hosted')) {
        useAlert().error(block?.title + ' is not allowed on self hosted. Please use our hosted version.')
        return
      }
      if (originalBlockDefinition?.auth_required && !useIsAuthenticated().isAuthenticated.value) {
        useAlert().error('Please login to add this block.')
        return
      }

      if (originalBlockDefinition?.max_count !== undefined) {
        const currentCount = this.content.properties.filter(prop => prop && prop.type === type).length
        if (currentCount >= originalBlockDefinition.max_count) {
          useAlert().error(`Only ${originalBlockDefinition.max_count} '${originalBlockDefinition.title}' block(s) allowed per form.`)
          return
        }
        openSettings = true 
      }
      
      this.blockForm.type = effectiveType
      this.blockForm.name = effectiveBlockDefinition?.default_block_name || 'New Block'
      const newBlock = this.prefillDefault({ ...this.blockForm.data() })
      newBlock.id = generateUUID()
      newBlock.hidden = false
      newBlock.help_position = "below_input"

      // If the type was changed due to actual_input, apply original type's change settings
      if (originalBlockDefinition?.actual_input && originalBlockDefinition?.type_change_settings) {
        Object.assign(newBlock, originalBlockDefinition.type_change_settings)
      }

      if (effectiveBlockDefinition?.default_values) {
        Object.assign(newBlock, effectiveBlockDefinition.default_values)
      }

      const insertIndex = this.determineInsertIndex(index)
      
      const newFields = clonedeep(this.content.properties || [])
      newFields.splice(insertIndex, 0, newBlock)
      // Use setProperties to ensure content update triggers computed service update
      this.setProperties(newFields)
      
      if (openSettings) {
        this.openSettingsForField(insertIndex)
      }
    },
    addGeneratedFields(fields) {
      if (!this.content) return
      
      const insertIndex = this.determineInsertIndex(null, true)
      
      const newFields = clonedeep(this.content.properties || [])
      newFields.splice(insertIndex, 0, ...fields)
      this.setProperties(newFields)
    },
    removeField(field) {
      this.internalRemoveField(field)
    },
    internalRemoveField(field) {
      const index = this.objectToIndex(field)

      if (index !== -1 && this.content?.properties) {
        useAlert().success('Ctrl + Z to undo',10000,{
          title: 'Field removed',
          actions: [{
            label: 'Undo',
            icon:"i-material-symbols-undo",
            onclick: () => {
              this.undo()
            }
          }]
        })
        const newProps = [...this.content.properties]
        newProps.splice(index, 1)
        this.setProperties(newProps)
      }
    },
    moveField(oldIndex, newIndex) {
      if (!this.content?.properties || oldIndex === newIndex) return
      
      const newFields = clonedeep(this.content.properties)
      if (oldIndex < 0 || oldIndex >= newFields.length) return
      
      const field = newFields.splice(oldIndex, 1)[0]
      
      const validNewIndex = Math.max(0, Math.min(newIndex, newFields.length))
      
      newFields.splice(validNewIndex, 0, field)
      this.setProperties(newFields)
    }
  },
  history: {
    ignoreKeys: ['structureService', 'blockForm']
  }
})
