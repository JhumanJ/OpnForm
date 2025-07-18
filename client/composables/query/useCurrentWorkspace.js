import { computed, watch } from 'vue'


/**
 * Dedicated composable for reactive current workspace state
 * Separates current workspace logic from CRUD operations
 */
export function useCurrentWorkspace() {
  const appStore = useAppStore()
  const { list } = useWorkspaces()
  const { isAuthenticated } = useIsAuthenticated()
  
  const workspacesQuery = list({
    enabled: () => isAuthenticated.value
  })
  
  // Watch for workspaces data and auto-select first workspace if none is current
  watch(
    () => workspacesQuery.data.value,
    (workspaces) => {
      if (!workspaces) {
        return
      }

      const currentWorkspaceExists = appStore.currentId && workspaces.some(ws => ws.id === appStore.currentId)

      if (workspaces.length === 0) {
        appStore.setCurrentId(null)
      } else if (!currentWorkspaceExists) {
        appStore.setCurrentId(workspaces[0].id)
      }
    },
    { immediate: true }
  )
  
  // Reactive current workspace - combines store state with query data
  const current = computed(() => {
    const currentId = appStore.currentId
    const workspaces = workspacesQuery.data.value
    
    if (!currentId || !workspaces) {
      return null
    }
    
    return workspaces.find(workspace => workspace.id === currentId) || null
  })
  
  // Derived state for easier consumption
  const isLoading = computed(() => {
    return workspacesQuery.isLoading.value && !workspacesQuery.data.value
  })
  
  const isError = computed(() => {
    return workspacesQuery.isError.value
  })
  
  const error = computed(() => {
    return workspacesQuery.error.value
  })
  
  // Helper to check if user has a current workspace
  const hasWorkspace = computed(() => {
    return !!current.value
  })
  
  // Helper to get current workspace ID
  const currentId = computed(() => {
    return appStore.currentId
  })
  
  // Helper to switch workspace (delegates to store)
  const switchTo = (workspaceId) => {
    appStore.setCurrentId(workspaceId)
  }
  
  return {
    // Primary state
    current,
    currentId,
    
    // Status indicators  
    isLoading,
    isError,
    error,
    hasWorkspace,
    
    // Actions
    switchTo,
  }
} 