import { computed, ref, watch, watchEffect, nextTick } from 'vue'

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
  const isUpdatingFromPreferences = ref(false)
  
  // Initialize and sync visibility from preferences
  const updateVisibilityFromPreferences = () => {
    isUpdatingFromPreferences.value = true
    const visibility = {}
    const configCols = columnConfigurations.value || []
    configCols.forEach(col => {
      const pref = getColumnPreference(col.id)
      // Default visibility: visible for regular columns, hidden for removed columns
      visibility[col.id] = col.isRemoved ? false : pref.visible
    })
    columnVisibility.value = visibility
    isUpdatingFromPreferences.value = false
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updateVisibilityFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnVisibility, (newVisibility, oldVisibility) => {
    if (isUpdatingFromPreferences.value) return
    
    // Only update if the values actually changed
    const changedEntries = Object.entries(newVisibility).filter(([columnId, visible]) => {
      return oldVisibility?.[columnId] !== visible
    })
    
    if (changedEntries.length > 0) {
      nextTick(() => {
        changedEntries.forEach(([columnId, visible]) => {
          setColumnPreference(columnId, { visible })
        })
      })
    }
  }, { deep: true })

  // Reactive column pinning - use ref to avoid circular updates
  const columnPinning = ref({ left: [], right: ['actions'] })
  const isUpdatingPinningFromPreferences = ref(false)
  
  // Initialize and sync pinning from preferences
  const updatePinningFromPreferences = () => {
    isUpdatingPinningFromPreferences.value = true
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
    isUpdatingPinningFromPreferences.value = false
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updatePinningFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnPinning, (newPinning, oldPinning) => {
    if (isUpdatingPinningFromPreferences.value) return
    
    // Only update if the pinning actually changed
    const leftChanged = JSON.stringify(newPinning.left) !== JSON.stringify(oldPinning?.left)
    const rightChanged = JSON.stringify(newPinning.right) !== JSON.stringify(oldPinning?.right)
    
    if (leftChanged || rightChanged) {
      nextTick(() => {
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
      })
    }
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
  const isUpdatingSizingFromPreferences = ref(false)
  
  // Initialize and sync sizing from preferences
  const updateSizingFromPreferences = () => {
    isUpdatingSizingFromPreferences.value = true
    columnSizing.value = columnPreferences.preferences.value.columnSizing || {}
    isUpdatingSizingFromPreferences.value = false
  }
  
  // Watch for preference changes and update ref
  watchEffect(() => {
    updateSizingFromPreferences()
  })
  
  // Watch for table changes and update preferences
  watch(columnSizing, (newSizing, oldSizing) => {
    if (isUpdatingSizingFromPreferences.value) return
    
    // Only update if the sizing actually changed
    const sizingChanged = JSON.stringify(newSizing) !== JSON.stringify(oldSizing)
    
    if (sizingChanged) {
      nextTick(() => {
        columnPreferences.setColumnSizing(newSizing)
      })
    }
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