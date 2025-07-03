import { defineStore } from "pinia"

export const useWorkspacesStore = defineStore("workspaces", () => {
  const storedWorkspaceId = useCookie("currentWorkspace")
  const currentId = ref(storedWorkspaceId.value)

  const setCurrentId = (id) => {
    currentId.value = id
    storedWorkspaceId.value = id
  }

  // Legacy method for compatibility - will be deprecated
  const getCurrent = computed(() => {
    // This will be replaced by useWorkspaces().current() in components
    console.warn('workspacesStore.getCurrent is deprecated. Use useWorkspaces().current() instead.')
    
    // Try to get from TanStack Query cache if available
    if (process.client && window.__VUE_QUERY_CLIENT__) {
      const queryClient = window.__VUE_QUERY_CLIENT__
      return queryClient.getQueryData(['workspaces', 'current']) || null
    }
    
    return null
  })

  // Legacy method for compatibility - will be deprecated
  const getByKey = (id) => {
    console.warn('workspacesStore.getByKey is deprecated. Use useWorkspaces().getWorkspaceById() instead.')
    
    // Try to get from TanStack Query cache if available
    if (process.client && window.__VUE_QUERY_CLIENT__) {
      const queryClient = window.__VUE_QUERY_CLIENT__
      return queryClient.getQueryData(['workspaces', id]) || null
    }
    
    return null
  }

  // Legacy method for compatibility during migration
  const save = (items) => {
    console.warn('workspacesStore.save is deprecated. Workspaces are now managed by TanStack Query.')
    
    // Set current workspace if none is set and we have workspaces
    if (!currentId.value && items && items.length > 0) {
      setCurrentId(items[0].id)
    }
  }

  // Legacy property for compatibility
  const loading = ref(false)

  return {
    currentId: readonly(currentId),
    setCurrentId,
    getCurrent,
    getByKey,
    save,
    loading: readonly(loading)
  }
})
