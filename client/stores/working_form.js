import { defineStore } from "pinia"
import clonedeep from "clone-deep"
import { generateUUID } from "~/lib/utils.js"
import blocksTypes from "~/data/blocks_types.json"
import { useAlert } from '~/composables/useAlert'
import { useAuthStore } from '~/stores/auth'
import { FormStructureService } from '~/lib/forms/services/FormStructureService'

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
  getters: {
    // Get all blocks/properties in the form
    formBlocks() {
      return this.content?.properties || []
    },
    
    // Add getter for current page fields based on formPageIndex and raw properties
    // Note: This ignores hidden status, unlike the real StructureService
    currentPageFields() {
      if (!this.content?.properties) return [];
      
      let pageStartIndex = 0;
      let currentPage = 0;
      for (let i = 0; i < this.content.properties.length; i++) {
        if (currentPage === this.formPageIndex) {
            // Find the end of the current page
            let pageEndIndex = this.content.properties.length - 1;
            for (let j = i; j < this.content.properties.length; j++) {
                if (this.content.properties[j]?.type === 'nf-page-break') {
                    pageEndIndex = j;
                    break;
                }
            }
            return this.content.properties.slice(pageStartIndex, pageEndIndex + 1);
        }
        
        if (this.content.properties[i]?.type === 'nf-page-break') {
          currentPage++;
          pageStartIndex = i + 1;
        }
      }
      // Fallback for last page if no breaks found after target index
      return this.content.properties.slice(pageStartIndex);
    },

    // Simple page count based on raw properties (ignores hidden status)
    simplePageCount() {
       if (!this.content?.properties) return 1;
       let count = 1;
       this.content.properties.forEach(prop => {
           if (prop?.type === 'nf-page-break') {
               count++;
           }
       });
       return count;
    }
  },
  actions: {
    set(form) {
      this.content = form
      // Ensure page index is valid after loading new form
      this.formPageIndex = Math.min(this.formPageIndex, this.simplePageCount - 1);
    },
    setProperties(properties) {
      if (!this.content) return
      this.content.properties = [...properties]
      // Ensure page index is valid after properties change
      this.formPageIndex = Math.min(this.formPageIndex, this.simplePageCount - 1);
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
        this.setPageIndex(this.selectedFieldIndex)
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
      this.formPageIndex = 0
      this.blockForm = null
    },

    resetBlockForm() {
      this.blockForm = useForm({
        type: null,
        name: null,
      })
    },

    /**
     * Determine where to insert a new block based on current page index and selection.
     * This provides a basic insertion point; drag/drop uses its own index.
     * @param {number|null} explicitIndex - Optional explicit index to insert at
     * @returns {number} The index where the block should be inserted relative to all properties
     */
    determineInsertIndex(explicitIndex) {
      if (explicitIndex !== null && typeof explicitIndex === 'number') {
        return explicitIndex;
      }

      if (this.selectedFieldIndex !== null && this.selectedFieldIndex >= 0) {
          return this.selectedFieldIndex + 1;
      }
      
      // Fallback: Insert at the end of the current page
      if (!this.content?.properties) return 0;

      let pageStartIndex = 0;
      let currentPage = 0;
      for (let i = 0; i < this.content.properties.length; i++) {
          if (currentPage === this.formPageIndex) {
              // Find the end index of the current page
              for (let j = i; j < this.content.properties.length; j++) {
                  if (this.content.properties[j]?.type === 'nf-page-break') {
                      return j + 1; // Insert after the page break (which is end of page)
                  }
              }
              // If no break found, insert at the end of the form
              return this.content.properties.length;
          }
          
          if (this.content.properties[i]?.type === 'nf-page-break') {
              currentPage++;
              pageStartIndex = i + 1; // This isn't actually used in this logic path
          }
      }
      
      // Default to end of form if page calculation fails
      return this.content?.properties?.length || 0;
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
      if (!this.blockForm) {
        this.resetBlockForm()
      }
      if (!this.content) return
      
      const block = blocksTypes[type]
      if (block?.self_hosted !== undefined && !block.self_hosted && useFeatureFlag('self_hosted')) {
        useAlert().error(block?.title + ' is not allowed on self hosted. Please use our hosted version.')
        return
      }

      if (block?.auth_required && !useAuthStore().check) {
        useAlert().error('Please login first to add this block')
        return
      }

      if (block?.max_count !== undefined) {
        const currentCount = this.content.properties.filter(prop => prop && prop.type === type).length
        if (currentCount >= block.max_count) {
          useAlert().error(`Only ${block.max_count} '${block.title}' block(s) allowed per form.`)
          return
        }
        openSettings = true 
      }
      
      this.blockForm.type = type
      this.blockForm.name = blocksTypes[type]?.default_block_name || 'New Block'
      const newBlock = this.prefillDefault({ ...this.blockForm.data() })
      newBlock.id = generateUUID()
      newBlock.hidden = false
      newBlock.help_position = "below_input"

      if (blocksTypes[type]?.default_values) {
        Object.assign(newBlock, blocksTypes[type].default_values)
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
            click: () => {
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
      
      // Simplified: Just move in the flat array. Page consistency relies on UI interaction.
      const newFields = clonedeep(this.content.properties)
      if (oldIndex < 0 || oldIndex >= newFields.length) return
      
      const field = newFields.splice(oldIndex, 1)[0]
      
      const validNewIndex = Math.max(0, Math.min(newIndex, newFields.length))
      
      newFields.splice(validNewIndex, 0, field)
      this.setProperties(newFields)
    },
    
    /**
     * Sets the current page index. Calculation of the target page should happen externally 
     * (using FormManager's StructureService) before calling this action.
     * @param {number} targetPageIndex - The page index to navigate to.
     */
    setPageIndex(targetPageIndex) {
        const newIndex = Math.max(0, Math.min(targetPageIndex, this.simplePageCount - 1));
        if (this.formPageIndex !== newIndex) {
            console.log(`WorkingFormStore: Setting page index to ${newIndex}`);
            this.formPageIndex = newIndex;
        }
    }
  },
  history: {}
})
