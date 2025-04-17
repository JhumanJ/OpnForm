import FormLogicPropertyResolver from "~/lib/forms/FormLogicPropertyResolver.js"

export class FormStructureService {
    constructor(form) {
      this.form = form
    }
  
    /**
     * Get all field groups based on page breaks
     */
    getFieldGroups() {
      if (!this.form.properties) return []
      
      const groups = []
      let currentGroup = []
  
      this.form.properties.forEach((field) => {
        currentGroup.push(field)
        if (field.type === 'nf-page-break' && !this.isFieldHidden(field)) {
          groups.push([...currentGroup])
          currentGroup = []
        }
      })
  
      if (currentGroup.length > 0) {
        groups.push(currentGroup)
      }
  
      return groups
    }
  
    /**
     * Get fields for a specific page
     */
    getPageFields(pageIndex) {
      return this.getFieldGroups()[pageIndex] || []
    }
  
    /**
     * Get total number of pages
     */
    getPageCount() {
      return this.getFieldGroups().length
    }
  
    /**
     * Check if given page is the last page
     */
    isLastPage(pageIndex) {
      return pageIndex === this.getPageCount() - 1
    }
  
    /**
     * Get indices of all page breaks
     */
    getPageBreakIndices() {
      if (!this.form.properties || this.form.properties.length === 0) return []
  
      const indices = []
      this.form.properties.forEach((prop, index) => {
        if (prop.type === 'nf-page-break' && !this.isFieldHidden(prop)) {
          indices.push(index)
        }
      })
  
      return indices
    }
  
    /**
     * Get page boundaries (start and end indices)
     */
    getPageBoundaries() {
      if (!this.form.properties || this.form.properties.length === 0) {
        return [{ start: 0, end: 0 }]
      }
  
      const boundaries = []
      const breaks = this.getPageBreakIndices()
  
      if (breaks.length === 0) {
        return [{
          start: 0,
          end: this.form.properties.length - 1
        }]
      }
  
      let startIndex = 0
  
      breaks.forEach(breakIndex => {
        boundaries.push({
          start: startIndex,
          end: breakIndex
        })
        startIndex = breakIndex + 1
      })
  
      boundaries.push({
        start: startIndex,
        end: this.form.properties.length - 1
      })
  
      return boundaries
    }
  
    /**
     * Get the page number for a given field index
     */
    getPageForField(fieldIndex) {
      if (fieldIndex === -1 || fieldIndex === null) return 0
  
      if (!this.form.properties ||
          this.form.properties.length === 0 ||
          fieldIndex >= this.form.properties.length) {
        return 0
      }
  
      if (this.getPageBreakIndices().length === 0) {
        return 0
      }
  
      for (let i = 0; i < this.getPageBoundaries().length; i++) {
        const { start, end } = this.getPageBoundaries()[i]
        if (fieldIndex >= start && fieldIndex <= end) {
          return i
        }
      }
  
      return this.getPageBoundaries().length - 1
    }
  
    /**
     * Check if a page has a payment block
     */
    hasPaymentBlock(pageIndex) {
      return this.getPageFields(pageIndex).some(field => field.type === 'payment')
    }
  
    /**
     * Get the payment block for a page if it exists
     */
    getPaymentBlock(pageIndex) {
      return this.getPageFields(pageIndex).find(field => field.type === 'payment')
    }
  
    /**
     * Check if a field should be hidden based on form logic
     */
    isFieldHidden(field, formData = {}) {
      return (new FormLogicPropertyResolver(field, formData)).isHidden()
    }
  
    /**
     * Get the page break block for a page if it exists
     */
    getPageBreakBlock(pageIndex) {
      const fields = this.getPageFields(pageIndex)
      if (!fields?.length) return null
      
      // Find the page break block in the current page
      const pageBreak = fields.find(field => field.type === 'nf-page-break')
      return pageBreak || null
    }
  
    /**
     * Get the previous page break block
     */
    getPreviousPageBreakBlock(pageIndex) {
      if (pageIndex === 0) return null
      const previousFields = this.getPageFields(pageIndex - 1)
      if (!previousFields?.length) return null
  
      // Find the page break block in the previous page
      const pageBreak = previousFields.find(field => field.type === 'nf-page-break')
      return pageBreak || null
    }
  
    /**
     * Determine insert index for a new field
     */
    determineInsertIndex(selectedFieldIndex, currentPageIndex, explicitIndex = null) {
      // If an explicit index is provided, use that
      if (explicitIndex !== null && typeof explicitIndex === 'number') {
        return explicitIndex
      }
  
      // If a field is selected, insert after it
      if (selectedFieldIndex !== null && selectedFieldIndex !== undefined) {
        return selectedFieldIndex + 1
      }
  
      // Early validation
      if (!this.form.properties || this.form.properties.length === 0) {
        return 0
      }
  
      const pageBreaks = this.getPageBreakIndices()
  
      // If no page breaks, insert at the end of the form
      if (pageBreaks.length === 0) {
        return this.form.properties.length
      }
  
      // For first page
      if (currentPageIndex === 0) {
        return pageBreaks[0]
      }
  
      // For pages after the first one
      const nextPageBreakIndex = pageBreaks[currentPageIndex] || this.form.properties.length
  
      if (currentPageIndex >= pageBreaks.length) {
        return this.form.properties.length
      }
  
      return nextPageBreakIndex
    }
  
    /**
     * Get target field index for drag and drop operations
     */
    getTargetFieldIndex(currentFieldPageIndex, selectedFieldIndex, formPageIndex) {
      if (formPageIndex > 0) {
        const previousGroups = this.getFieldGroups()
          .slice(0, formPageIndex)
          .reduce((sum, group) => sum + group.length, 0)
        return previousGroups + currentFieldPageIndex
      }
      return currentFieldPageIndex
    }
  }
  