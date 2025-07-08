import { useStorage } from '@vueuse/core'
import { computed, toValue, readonly } from 'vue'

export function useTableColumnPreferences(formId) {
  // Generate storage key
  const storageKey = computed(() => {
    if (import.meta.server) return ''
    return `table-prefs-${toValue(formId)}`
  })

  // Default preferences structure
  const defaultPreferences = {
    columnPreferences: {},
    columnOrder: [],
    columnSizing: {},
    version: 1 // for future migrations
  }

  // Main reactive storage
  const preferences = useStorage(
    storageKey,
    defaultPreferences,
    import.meta.server ? undefined : localStorage,
    {
      mergeDefaults: true,
      serializer: {
        read: (v) => {
          try {
            return JSON.parse(v) || defaultPreferences
          } catch {
            return defaultPreferences
          }
        },
        write: (v) => JSON.stringify(v)
      }
    }
  )

  // Individual column state getters/setters
  const getColumnPreference = (columnId) => {
    if (import.meta.server) {
      return {
        visible: true,
        pinned: false,
        wrapped: false,
        order: 999,
        size: null
      }
    }
    return preferences.value.columnPreferences[columnId] || {
      visible: true,
      pinned: false,
      wrapped: false,
      order: 999,
      size: null
    }
  }

  const setColumnPreference = (columnId, updates) => {
    if (import.meta.server) return
    preferences.value.columnPreferences[columnId] = {
      ...getColumnPreference(columnId),
      ...updates
    }
  }

  // Batch operations
  const setColumnOrder = (newOrder) => {
    if (import.meta.server) return
    preferences.value.columnOrder = newOrder
    // Update order in preferences
    newOrder.forEach((columnId, index) => {
      setColumnPreference(columnId, { order: index })
    })
  }

  const setColumnSizing = (newSizing) => {
    if (import.meta.server) return
    preferences.value.columnSizing = { ...newSizing }
  }

  const resetPreferences = () => {
    if (import.meta.server) return
    preferences.value = { ...defaultPreferences }
  }

  // Migration helper for future versions
  const migratePreferences = () => {
    if (import.meta.server) return
    if (preferences.value.version < 1) {
      // Future migration logic here
      preferences.value.version = 1
    }
  }

  // Initialize migration on first load
  if (import.meta.client) {
    migratePreferences()
  }

  return {
    preferences: readonly(preferences),
    getColumnPreference,
    setColumnPreference,
    setColumnOrder,
    setColumnSizing,
    resetPreferences,
    migratePreferences
  }
} 