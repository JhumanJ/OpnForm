import { defineStore } from "pinia"

export const useWorkspacesStore = defineStore("workspaces", () => {
  const storedWorkspaceId = useCookie("currentWorkspace")
  const currentId = ref(storedWorkspaceId.value)

  const setCurrentId = (id) => {
    currentId.value = id
    storedWorkspaceId.value = id
  }

  /**
   * Called when workspaces are loaded to set current workspace if none is set
   * This is the only workspace-related logic the store should handle
   */
  const onWorkspacesLoaded = (workspaces) => {
    if (!currentId.value && workspaces && workspaces.length > 0) {
      setCurrentId(workspaces[0].id)
    }
  }

  return {
    currentId,
    setCurrentId,
    onWorkspacesLoaded,
  }
})
