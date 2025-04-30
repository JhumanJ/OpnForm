/**
 * @fileoverview Service responsible for analyzing and managing the structural aspects of a form,
 * including page breaks, field grouping, page boundaries, and determining field locations.
 */
import FormLogicPropertyResolver from '~/lib/forms/FormLogicPropertyResolver.js'
import { computed } from 'vue'

// services/form/services/FormStructureService.js
export class FormStructureService {
  constructor(formConfig, managerState) {
    this.form = formConfig || { properties: [] } // Ensure form and properties exist
    this.managerState = managerState // Store the reactive state

    // Define computed properties using Vue's computed
    this.computedFieldGroups = computed(() => {
        console.log("Recomputing Field Groups due to properties change:", this.form.properties?.length);
        return this._calculateFieldGroups();
    });
    this.computedPageCount = computed(() => {
        const groups = this.computedFieldGroups.value;
        const count = groups ? groups.length : 0;
        console.log(`[FormStructureService] computedPageCount evaluated: count=${count}`);
        return count;
    });
    this.computedPageBoundaries = computed(() => {
        console.log("Recomputing Page Boundaries due to properties change:", this.form.properties?.length);
        return this._calculatePageBoundaries();
    });

    this.computedCurrentPageBreak = computed(() => {
      // Directly depend on computedFieldGroups and managerState
      const groups = this.computedFieldGroups.value;
      if (!this.managerState || !groups) return null; 

      const currentPageIndex = this.managerState.currentPage;
      if (currentPageIndex < 0 || currentPageIndex >= groups.length) return null; // Index out of bounds

      const currentPageFields = groups[currentPageIndex] || [];
      if (currentPageFields.length === 0) return null;
      
      const lastField = currentPageFields[currentPageFields.length - 1];
      return (lastField && lastField.type === 'nf-page-break') ? lastField : null;
    });

    this.computedPreviousPageBreak = computed(() => {
      // Directly depend on computedFieldGroups and managerState
      const groups = this.computedFieldGroups.value;
      if (!this.managerState || !groups || this.managerState.currentPage <= 0) return null;

      const previousPageIndex = this.managerState.currentPage - 1;
      if (previousPageIndex < 0 || previousPageIndex >= groups.length) return null; // Index out of bounds

      const previousPageFields = groups[previousPageIndex] || [];
      if (previousPageFields.length === 0) return null;
      
      const lastField = previousPageFields[previousPageFields.length - 1];
      return (lastField && lastField.type === 'nf-page-break') ? lastField : null;
    });

    // NEW: computedIsLastPage
    this.computedIsLastPage = computed(() => {
        const pageIndex = this.managerState?.currentPage;
        const pageCount = this.computedPageCount.value;
        // Add checks for valid pageIndex and pageCount
        if (pageIndex === undefined || pageIndex === null || pageCount === undefined) {
            console.warn('[FormStructureService] computedIsLastPage: Invalid state, returning default true.');
            return true; // Default to true if state is invalid to avoid blocking simple forms
        }
        const result = pageIndex === pageCount - 1;
        console.log(`[FormStructureService] computedIsLastPage evaluated: pageIndex=${pageIndex}, computedPageCount=${pageCount}, result=${result}`);
        return result;
    });
  }

  _calculateFieldGroups() {
    if (!this.form.properties || this.form.properties.length === 0) return [[]]

    const groups = []
    let currentGroup = []

    this.form.properties.forEach((field, index) => {
      // Add the current field to the group first
      currentGroup.push(field); 
      
      // If the field just added was a page break AND it's not hidden,
      // finalize the current group and start a new one for subsequent fields.
      if (field.type === 'nf-page-break' && !this.isFieldHidden(field)) {
        // Push the group that *ends* with this page break
        groups.push([...currentGroup]);

        // If this wasn't the very last field, reset for the next group
        if (index < this.form.properties.length - 1) {
            currentGroup = [];
        }
        // If it *was* the last field, currentGroup is left non-empty but shouldn't be added again
      }
    })

    // Add the final group ONLY if the loop finished and currentGroup contains fields
    // AND the last field processed was NOT a page break (because if it was, the group
    // including it was already pushed inside the loop).
    const lastProperty = this.form.properties[this.form.properties.length - 1];
    if (currentGroup.length > 0 && (!lastProperty || lastProperty.type !== 'nf-page-break' || this.isFieldHidden(lastProperty))) {
        groups.push(currentGroup);
    }
    
    // Ensure there's always at least one group, even if empty
    if (groups.length === 0) { 
        groups.push([]); // Handles empty form.properties case as well
    }

    return groups
  }

  getFieldGroups() {
    // Attempt to get the computed value
    const computedValue = this.computedFieldGroups.value;
    // If the computed value is ready and not null/undefined, return it
    if (computedValue) {
        return computedValue;
    }
    // Fallback: If computed value isn't ready, calculate synchronously
    console.warn("FormStructureService: computedFieldGroups not ready, calculating synchronously.");
    return this._calculateFieldGroups();
  }

  getPageFields(pageIndex) {
    const groups = this.getFieldGroups();
    // Add check: If groups itself is null/undefined, return empty array
    if (!groups) {
        console.warn("FormStructureService: getFieldGroups returned null/undefined");
        return [];
    }
    // Original logic: Return the specific page group or an empty array
    return groups[pageIndex] || [];
  }

  getPageBreakIndices() {
    return this._calculatePageBreakIndices();
  }

  _calculatePageBoundaries() {
    if (!this.form.properties || this.form.properties.length === 0) {
      return [{ start: 0, end: -1 }] // Empty form case
    }

    const boundaries = []
    const breaks = this._calculatePageBreakIndices()

    // If no page breaks, the single page spans all properties
    if (breaks.length === 0) {
      return [{
        start: 0,
        end: this.form.properties.length - 1
      }]
    }

    let startIndex = 0

    breaks.forEach(breakIndex => {
      // A page ends *at* the page break field itself
      boundaries.push({
        start: startIndex,
        end: breakIndex // Include the break field in the page it ends
      })
      startIndex = breakIndex + 1 // Next page starts after the break field
    })

    // Add the last page boundary if there are fields after the last break
    if (startIndex < this.form.properties.length) {
      boundaries.push({
        start: startIndex,
        end: this.form.properties.length - 1
      })
    } else if (boundaries.length > 0 && startIndex === this.form.properties.length) {
       // Handle case where last field is a page break - create an empty last page boundary maybe?
       // Or adjust the logic? Let's assume a page break at the end doesn't create a new empty page.
       // The current logic handles this correctly by not adding a final boundary if startIndex === length.
    }


    // If somehow no boundaries were created (e.g., only page break fields), return a default
    if (boundaries.length === 0) {
        return [{ start: 0, end: this.form.properties.length - 1 }]
    }

    return boundaries
  }

  getPageBoundaries() {
    return this.computedPageBoundaries.value;
  }

  getPageForField(fieldIndex) {
    if (fieldIndex === -1 || fieldIndex === null || fieldIndex === undefined) return 0

    if (!this.form.properties ||
        this.form.properties.length === 0 ||
        fieldIndex >= this.form.properties.length) {
      return 0 // Default to first page if index is out of bounds
    }

    const boundaries = this.getPageBoundaries()

    // If only one page boundary (no breaks or empty form)
    if (boundaries.length <= 1 && boundaries[0]?.start === 0) {
        return 0
    }


    for (let i = 0; i < boundaries.length; i++) {
      const { start, end } = boundaries[i]
      // Check if the fieldIndex falls within the start and end indices of the page boundary
      if (fieldIndex >= start && fieldIndex <= end) {
        return i
      }
    }

    // Fallback: if not found in any boundary (shouldn't happen with correct boundaries), return last page index
    return Math.max(0, boundaries.length - 1)
  }

  hasPaymentBlock(pageIndex) {
    return this.getPageFields(pageIndex).some(field => field.type === 'payment')
  }

  getPaymentBlock(pageIndex) {
    return this.getPageFields(pageIndex).find(field => field.type === 'payment')
  }

  /**
   * Gets the page break field object that defines the end of the *previous* page.
   * Returns null if on the first page or if the previous page doesn't end with a page break.
   * @param {number} currentPageIndex - The index of the *current* page.
   * @returns {Object|null} The page break field object or null.
   * @deprecated Use computedPreviousPageBreak instead
   */
  getPreviousPageBreak(currentPageIndex) {
    console.warn("`getPreviousPageBreak` is deprecated. Use `computedPreviousPageBreak`.");
    if (currentPageIndex <= 0) {
      return null; // No previous page
    }
    const previousPageIndex = currentPageIndex - 1;
    const previousPageFields = this.getPageFields(previousPageIndex);

    if (!previousPageFields || previousPageFields.length === 0) {
      return null; // Previous page doesn't exist or is empty
    }

    const lastFieldOfPreviousPage = previousPageFields[previousPageFields.length - 1];

    if (lastFieldOfPreviousPage && lastFieldOfPreviousPage.type === 'nf-page-break') {
      return lastFieldOfPreviousPage;
    }

    return null; // Previous page didn't end with a page break
  }

  isFieldHidden(field, formData = {}) {
    // Placeholder - Actual implementation depends on FormLogicPropertyResolver
    // Make sure FormLogicPropertyResolver is correctly imported and used
    try {
      // Pass the form data if needed by the resolver
      return (new FormLogicPropertyResolver(field, formData)).isHidden()
    } catch(e) {
        console.error("Error checking if field is hidden:", e);
        // Fallback if resolver fails or isn't available yet
        return field.hidden || false;
    }
  }

  // Added from original OpenForm - used for drag/drop target index calculation
  getTargetFieldIndex(currentFieldPageIndex, selectedFieldIndex, currentPageIndex) {
      return currentPageIndex > 0
        ? this.getFieldGroups().slice(0, currentPageIndex).reduce((sum, group) => sum + group.length, 0) + currentFieldPageIndex
        : currentFieldPageIndex;
  }

  determineInsertIndex(selectedFieldIndex, currentPageIndex, explicitIndex = null) {
    // If an explicit index is provided, use that
    if (explicitIndex !== null && typeof explicitIndex === 'number') {
      return explicitIndex
    }

    // If a field is selected, insert after it
    if (selectedFieldIndex !== null && selectedFieldIndex !== undefined && selectedFieldIndex >= 0) {
      return selectedFieldIndex + 1
    }

    // If no form properties, insert at index 0
    if (!this.form.properties || this.form.properties.length === 0) {
      return 0
    }
    
    const pageBoundaries = this.getPageBoundaries()
    
    // If no valid page boundaries or current page index is invalid, default to end of form
    if (!pageBoundaries || pageBoundaries.length === 0 || currentPageIndex >= pageBoundaries.length || currentPageIndex < 0) {
        return this.form.properties.length
    }

    const currentBoundary = pageBoundaries[currentPageIndex]
    
    // Insert at the end of the current page (after the last field of that page)
    return currentBoundary.end + 1
  }
} 