import { computed } from 'vue'

/**
 * Dedicated composable for reactive current workspace state
 * Separates current workspace logic from CRUD operations
 */
export function useCurrentWorkspace() {
  const workspacesStore = useWorkspacesStore()
  const { list } = useWorkspaces()
  
  // Get the workspace list query (but don't create a top-level query here)
  const workspacesQuery = list()
  
  // Reactive current workspace - combines store state with query data
  const current = computed(() => {
    const { currentId } = workspacesStore
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
    return workspacesStore.currentId
  })
  
  // Helper to switch workspace (delegates to store)
  const switchTo = (workspaceId) => {
    workspacesStore.setCurrentId(workspaceId)
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