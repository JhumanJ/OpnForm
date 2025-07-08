import { computed, ref, watch, watchEffect } from 'vue'

export function useTableState(form, columnPreferences, workspace = null) {
  // Ensure we're on the client side for preferences
  const { getColumnPreference, setColumnPreference } = columnPreferences

  // Computed column configurations
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

  // Reactive column visibility - use ref to avoid circular updates
  const columnVisibility = ref({})
  
  // Initialize and sync visibility from preferences
  const updateVisibilityFromPreferences = () => {
    const visibility = {}
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      const pref = getColumnPreference(col.id)
      // Default visibility: visible for regular columns, hidden for removed columns
      visibility[col.id] = col.isRemoved ? false : pref.visible
    })
    columnVisibility.value = visibility
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updateVisibilityFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnVisibility, (newVisibility) => {
    Object.entries(newVisibility).forEach(([columnId, visible]) => {
      setColumnPreference(columnId, { visible })
    })
  }, { deep: true })

  // Reactive column pinning - use ref to avoid circular updates
  const columnPinning = ref({ left: [], right: ['actions'] })
  
  // Initialize and sync pinning from preferences
  const updatePinningFromPreferences = () => {
    const pinning = { left: [], right: ['actions'] }
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      const pref = getColumnPreference(col.id)
      if (pref.pinned === 'left') {
        pinning.left.push(col.id)
      } else if (pref.pinned === 'right' && col.id !== 'actions') {
        pinning.right.unshift(col.id) // Add before actions
      }
    })
    columnPinning.value = pinning
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updatePinningFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnPinning, (newPinning) => {
    // Update all columns first
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      if (col.id !== 'actions') {
        setColumnPreference(col.id, { pinned: false })
      }
    })

    // Set new pinning
    newPinning.left?.forEach(columnId => {
      setColumnPreference(columnId, { pinned: 'left' })
    })
    newPinning.right?.forEach(columnId => {
      if (columnId !== 'actions') {
        setColumnPreference(columnId, { pinned: 'right' })
      }
    })
  }, { deep: true })

  // Column wrapping state
  const columnWrapping = computed(() => {
    const wrapping = {}
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      const pref = getColumnPreference(col.id)
      wrapping[col.id] = pref.wrapped
    })
    return wrapping
  })

  // Column sizing state - use ref to avoid circular updates
  const columnSizing = ref({})
  
  // Initialize and sync sizing from preferences
  const updateSizingFromPreferences = () => {
    columnSizing.value = columnPreferences.preferences.value.columnSizing || {}
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updateSizingFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnSizing, (newSizing) => {
    columnPreferences.setColumnSizing(newSizing)
  }, { deep: true })

  // Ordered columns for display
  const orderedColumns = computed(() => {
    try {
      const configCols = columnConfigurations.value || []
      const columns = Array.isArray(configCols) ? [...configCols] : []

      // Sort by order preference, then by original position
      columns.sort((a, b) => {
        const prefA = getColumnPreference(a.id)
        const prefB = getColumnPreference(b.id)

        if (prefA.order !== prefB.order) {
          return prefA.order - prefB.order
        }

        // Fallback to original order
        return columnConfigurations.value.indexOf(a) - columnConfigurations.value.indexOf(b)
      })

      return columns
    } catch (error) {
      console.error('Error in orderedColumns computed:', error)
      return []
    }
  })

  // Final table columns with all computed properties
  const tableColumns = computed(() => {
    try {
      // Ensure we have a valid array to work with
      const orderedCols = orderedColumns.value || []
      const cols = Array.isArray(orderedCols) ? [...orderedCols] : []

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
      if (import.meta.client && workspace && !workspace.is_readonly) {
        cols.push({
          id: 'actions',
          accessorKey: 'actions',
          header: 'Actions',
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

      return cols
    } catch (error) {
      console.error('Error in tableColumns computed:', error)
      return []
    }
  })

  return {
    columnConfigurations,
    columnVisibility,
    columnPinning,
    columnWrapping,
    columnSizing,
    orderedColumns,
    tableColumns
  }
} 