import { defineStore } from "pinia"

export const useWorkspacesStore = defineStore("workspaces", () => {
  const storedWorkspaceId = useCookie("currentWorkspace")
  const currentId = ref(storedWorkspaceId.value)

  const setCurrentId = (id) => {
    currentId.value = id
    storedWorkspaceId.value = id
  }

  return {
    currentId,
    setCurrentId,
  }
})
