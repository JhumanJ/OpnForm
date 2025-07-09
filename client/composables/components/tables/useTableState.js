import { computed } from 'vue'
import { useTableColumnPreferences } from './useTableColumnPreferences'
import debounce from 'debounce'

// Provides a composable for managing the state of a table component, including column visibility, pinning, sizing, and wrapping.
// It synchronizes table state with user preferences (such as column order and visibility) and supports dynamic updates based on form structure and workspace context.

export function useTableState(form, withActions = false) {
  // Internal preferences helper (hidden from consumers)
  const columnPreferences = useTableColumnPreferences(
    computed(() => form.value?.id || form.value?.slug)
  )

  const { getColumnPreference, setColumnPreference } = columnPreferences

  /* -------------------------------------------------------------------------- */
  /*                               Column config                               */
  /* -------------------------------------------------------------------------- */

  // Computed column configurations (base definition for every column)
  const columnConfigurations = computed(() => {
    try {
      if (!form.value?.properties || !Array.isArray(form.value.properties)) return []

      const baseColumns = form.value.properties
        .filter((field) => {
          return !['nf-text', 'nf-code', 'nf-page-break', 'nf-divider', 'nf-image'].includes(field.type)
        })
        .map(col => {
          const { columns: matrixColumns, ...rest } = col
          return {
            ...rest,
            ...(col.type === 'matrix' && { matrix_columns: matrixColumns }),
            id: col.id,
            accessorKey: col.id,
            header: col.name,
            isRemoved: false,
            enableResizing: true,
            minSize: 100,
            maxSize: 500,
          }
        })

       // Add removed properties
       if (form.value?.removed_properties) {
         form.value.removed_properties.forEach(property => {
           const { columns: matrixColumns, ...rest } = property
           baseColumns.push({
             ...(property.type === 'matrix' ? { ...rest, matrix_columns: matrixColumns } : { ...rest }),
             id: property.id,
             accessorKey: property.id,
             header: property.name,
             isRemoved: true,
             enableResizing: true,
             minSize: 100,
             maxSize: 500,
           })
         })
       }

       // Add created_at column if not present
       if (!baseColumns.find(property => property.id === 'created_at')) {
         baseColumns.push({
           id: 'created_at',
           accessorKey: 'created_at',
           header: 'Created at',
           type: 'date',
           enableResizing: true,
           minSize: 100,
           maxSize: 500,
         })
       }

       return baseColumns
    } catch (error) {
      console.error('Error in columnConfigurations computed:', error)
      return []
    }
  })

  /* -------------------------------------------------------------------------- */
  /*                       Writable computed bridges (reactive)                 */
  /* -------------------------------------------------------------------------- */

  // Column visibility
  const columnVisibility = computed({
    get() {
      // Establish dependency on the entire preferences object
      const prefs = columnPreferences.preferences.value
      const visibility = {}
      const configCols = columnConfigurations.value || []
      configCols.forEach(col => {
        const pref = prefs.columns[col.id] || {}
        // Use preference if set, otherwise default: visible for regular columns, hidden for removed columns
        const visible = pref.visible !== null && pref.visible !== undefined ? pref.visible : !col.isRemoved
        visibility[col.id] = visible
      })
      return visibility
    },
    set(newVisibility) {
      Object.entries(newVisibility).forEach(([columnId, visible]) => {
        const pref = getColumnPreference(columnId)
        const currentVisible = pref.visible !== null && pref.visible !== undefined 
          ? pref.visible 
          : !columnConfigurations.value.find(c => c.id === columnId)?.isRemoved
        if (currentVisible !== visible) {
          setColumnPreference(columnId, { visible })
        }
      })
    }
  })

  // Column pinning
  const columnPinning = computed({
    get() {
      const prefs = columnPreferences.preferences.value
      const pinning = { left: [], right: ['actions'] }
      const configCols = columnConfigurations.value || []
      configCols.forEach(col => {
        const pref = prefs.columns[col.id] || {}
        if (pref.pinned === 'left') pinning.left.push(col.id)
        if (pref.pinned === 'right') {
          if (col.id !== 'actions') pinning.right.unshift(col.id)
        }
      })
      return pinning
    },
    set(newPinning) {
      const { left = [], right = [] } = newPinning || {}
      const configCols = columnConfigurations.value || []

      // First, clear pinning for all applicable columns
      configCols.forEach(col => {
        if (col.id !== 'actions') setColumnPreference(col.id, { pinned: false })
      })

      // Apply new pinning
      left.forEach(id => setColumnPreference(id, { pinned: 'left' }))
      right.forEach(id => {
        if (id !== 'actions') setColumnPreference(id, { pinned: 'right' })
      })
    }
  })

  // Column sizing (table column resize)
  const columnSizing = computed({
    get() {
      return columnPreferences.preferences.value.globalSizing || {}
    },
    set(newSizing) {
      columnPreferences.setColumnSizing(newSizing)
    }
  })

  // Debounced resize handler to avoid excessive localStorage writes
  const debouncedSetColumnSizing = debounce((newSizing) => {
    columnPreferences.setColumnSizing(newSizing)
  }, 50) // 150ms debounce

  // Handle individual column resize - simple preference saving
  const handleColumnResize = (columnId, size) => {
    size = Math.min(Math.max(size, 80), 700)
    const currentSizing = columnSizing.value || {}
    const newSizing = {
      ...currentSizing,
      [columnId]: size
    }
    
    // Update the reactive state immediately for smooth UX
    columnSizing.value = newSizing
    
    // Save to preferences with debounce
    debouncedSetColumnSizing(newSizing)
  }



  // Column wrapping (read-only)
  const columnWrapping = computed(() => {
    const prefs = columnPreferences.preferences.value
    const wrapping = {}
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      const pref = prefs.columns[col.id] || {}
      wrapping[col.id] = pref.wrapped ?? false
    })
    return wrapping
  })

  /* -------------------------------------------------------------------------- */
  /*                              Column ordering                               */
  /* -------------------------------------------------------------------------- */

  // Helper function to set a specific column to a specific index
  const setColumnOrder = (columnId, newIndex) => {
    try {
      // Get the current list of VISIBLE column IDs, already in the correct order
      let visibleColumnIds = orderedColumns.value
        .filter(c => columnVisibility.value[c.id] !== false)
        .map(c => c.id)

      // Perform the move on this array
      const currentIndex = visibleColumnIds.indexOf(columnId)
      if (currentIndex > -1) {
        visibleColumnIds.splice(currentIndex, 1)
      }
      visibleColumnIds.splice(newIndex, 0, columnId)

      // Update the preferences based on the new order
      const allColumnIds = columnConfigurations.value.map(c => c.id)
      allColumnIds.forEach(id => {
        const visibleIndex = visibleColumnIds.indexOf(id)
        if (visibleIndex !== -1) {
          // It's visible, set its order
          columnPreferences.setColumnPreference(id, { order: visibleIndex })
        } else {
          // It's hidden, give it a high order number
          columnPreferences.setColumnPreference(id, { order: 9999 })
        }
      })
    } catch (error) {
      console.error('Error in setColumnOrder:', error)
    }
  }

  // Ordered columns for display
  const orderedColumns = computed({
    get() {
      try {
        const prefs = columnPreferences.preferences.value
        const configCols = columnConfigurations.value || []
        const columns = Array.isArray(configCols) ? [...configCols] : []

        // Sort by order preference, then by original position
        columns.sort((a, b) => {
          const prefA = prefs.columns[a.id] || {}
          const prefB = prefs.columns[b.id] || {}

          const orderA = prefA.order ?? 9999
          const orderB = prefB.order ?? 9999

          if (orderA !== orderB) {
            return orderA - orderB
          }

          // Fallback to original order
          return configCols.indexOf(a) - configCols.indexOf(b)
        })

        return columns
      } catch (error) {
        console.error('Error in orderedColumns computed:', error)
        return []
      }
    },
    set(newOrderColumns) {
      // Accepts an array of column objects OR ids; we extract ids
      const ids = newOrderColumns.map(c => (typeof c === 'string' ? c : c.id))
      columnPreferences.setColumnsOrder(ids)
    }
  })

  /* -------------------------------------------------------------------------- */
  /*                           Final columns (data table)                       */
  /* -------------------------------------------------------------------------- */

  const tableColumns = computed(() => {
    try {
      // Ensure we have a valid array to work with  
      const cols = orderedColumns.value && Array.isArray(orderedColumns.value) ? [...orderedColumns.value] : []

      // Add status column if needed
      if (form.value?.is_pro && (form.value.enable_partial_submissions ?? false)) {
        cols.push({
          id: 'status',
          accessorKey: 'status',
          header: 'Status',
          enableColumnFilter: true,
          filterFn: 'equals',
          enableResizing: true,
          minSize: 100,
          maxSize: 500,
        })
      }

      // Add actions column if workspace is not readonly
      if (import.meta.client && withActions) {
        cols.push({
          id: 'actions',
          accessorKey: 'actions',
          header: '',
          enableResizing: false,
          size: 80,
          meta: {
            class: {
              th: 'bg-transparent',
              td: 'backdrop-blur-xs bg-white/70'
            }
          }
        })
      }

      return cols ?? []
    } catch (error) {
      console.error('Error in tableColumns computed:', error)
      return []
    }
  })

  /* -------------------------------------------------------------------------- */
  /*                              Public API                                   */
  /* -------------------------------------------------------------------------- */

  return {
    // Table state (for v-model binding)
    columnConfigurations,
    columnVisibility,
    columnPinning,
    columnWrapping,
    columnSizing,
    orderedColumns,
    tableColumns,
    
    // Preference helpers (for components)
    getColumnPreference,
    setColumnPreference,
    resetPreferences: columnPreferences.resetPreferences,
    resetColumn: columnPreferences.resetColumn,
    toggleColumnVisibility: columnPreferences.toggleColumnVisibility,
    toggleColumnPin: columnPreferences.toggleColumnPin,
    toggleColumnWrap: columnPreferences.toggleColumnWrap,
    setColumnsOrder: columnPreferences.setColumnsOrder,
    setColumnOrder, // Use our local setColumnOrder function
    handleColumnResize,
  }
} 