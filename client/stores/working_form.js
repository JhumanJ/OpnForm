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
  getters: {
    // Get all blocks/properties in the form
    formBlocks() {
      return this.content?.properties || []
    },
    
    // Get page break indices to determine page boundaries
    pageBreakIndices() {
      if (!this.content?.properties || this.content.properties.length === 0) return []
      
      // Find all indices of page break blocks
      const indices = []
      this.content.properties.forEach((prop, index) => {
        if (prop.type === 'nf-page-break' && !prop.hidden) {
          indices.push(index)
        }
      })
      
      return indices
    },
    
    // Calculate page boundaries (start/end indices for each page)
    pageBoundaries() {
      if (!this.content?.properties || this.content.properties.length === 0) {
        return [{ start: 0, end: 0 }]
      }
      
      const boundaries = []
      const breaks = this.pageBreakIndices
      
      // If no page breaks, return a single page boundary
      if (breaks.length === 0) {
        return [{ 
          start: 0, 
          end: this.formBlocks.length - 1 
        }]
      }
      
      // First page starts at 0
      let startIndex = 0
      
      // For each page break, create a boundary
      breaks.forEach(breakIndex => {
        boundaries.push({
          start: startIndex,
          end: breakIndex
        })
        startIndex = breakIndex + 1
      })
      
      // Add the last page
      boundaries.push({
        start: startIndex,
        end: this.formBlocks.length - 1
      })
      
      return boundaries
    },
    
    // Get the current page's boundary
    currentPageBoundary() {
      return this.pageBoundaries[this.formPageIndex] || { start: 0, end: this.formBlocks.length - 1 }
    },
    
    // Count total pages
    pageCount() {
      return this.pageBoundaries.length
    },
    
    // Whether this is the last page
    isLastPage() {
      return this.formPageIndex === this.pageCount - 1
    }
  },
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
      
      // Set the page to the one containing this field
      // But only do this when initially opening settings, not during editing
      if (typeof field === 'number' || (typeof field === 'object' && field !== null)) {
        // Only navigate to the field's page if we're newly selecting it
        // Not if we're just updating an already selected field
        const previousIndex = this.selectedFieldIndex
        const currentIndex = this.objectToIndex(field)
        
        if (previousIndex !== currentIndex) {
          this.setPageForField(this.selectedFieldIndex)
        }
      }
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

    /**
     * Determine where to insert a new block
     * @param {number|null} explicitIndex - Optional explicit index to insert at
     * @returns {number} The index where the block should be inserted
     */
    determineInsertIndex(explicitIndex) {
      // If an explicit index is provided, use that
      if (explicitIndex !== null && typeof explicitIndex === 'number') {
        return explicitIndex
      }
      
      // If a field is selected, insert after it
      // This handles the case when adding from a field's "Add new field" button
      if (this.selectedFieldIndex !== null && this.selectedFieldIndex !== undefined) {
        return this.selectedFieldIndex + 1
      }
      
      // Early validation
      if (!this.content?.properties || this.content.properties.length === 0) {
        return 0
      }
      
      // Get the current page's boundaries
      const pageBreaks = this.pageBreakIndices
      
      // If no page breaks, insert at the end of the form
      if (pageBreaks.length === 0) {
        return this.content.properties.length
      }
      
      // For first page
      if (this.formPageIndex === 0) {
        return pageBreaks[0]
      }
      
      // For pages after the first one
      // Find the end of the current page (the page break index)
      const nextPageBreakIndex = pageBreaks[this.formPageIndex] || this.content.properties.length
      
      // Insert at the end of the current page, right before the next page break
      // If this is the last page, insert at the very end
      if (this.formPageIndex >= pageBreaks.length) {
        return this.content.properties.length
      }
      
      return nextPageBreakIndex
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
      newBlock.help_position = "below_input"

      // Apply default values from blocks_types.json if they exist
      if (blocksTypes[type]?.default_values) {
        Object.assign(newBlock, blocksTypes[type].default_values)
      }

      // Determine the insert index
      const insertIndex = this.determineInsertIndex(index)
      
      // Insert at the determined position
      const newFields = clonedeep(this.content.properties)
      newFields.splice(insertIndex, 0, newBlock)
      this.content.properties = newFields
      
      if (openSettings) {
        this.openSettingsForField(insertIndex)
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
    },
    
    /**
     * Find which page a field belongs to and navigate to it
     * @param {number} fieldIndex - The index of the field to navigate to
     */
    setPageForField(fieldIndex) {
      if (fieldIndex === -1 || fieldIndex === null) return
      
      // Early return if no fields or field is out of range
      if (!this.content?.properties || 
          this.content.properties.length === 0 || 
          fieldIndex >= this.content.properties.length) {
        return
      }
      
      // If there are no page breaks, everything is on page 0
      if (this.pageBreakIndices.length === 0) {
        this.formPageIndex = 0
        return
      }
      
      // Find which page contains this field
      for (let i = 0; i < this.pageBoundaries.length; i++) {
        const { start, end } = this.pageBoundaries[i]
        if (fieldIndex >= start && fieldIndex <= end) {
          // Only set page if it's different to avoid unnecessary rerenders
          if (this.formPageIndex !== i) {
            this.formPageIndex = i
          }
          return
        }
      }
      
      // Fallback to last page if field not found in any boundaries
      const lastPageIndex = this.pageBoundaries.length - 1
      if (this.formPageIndex !== lastPageIndex) {
        this.formPageIndex = lastPageIndex
      }
    }
  },
  history: {}
})
