import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"

export const workspaceEndpoint = "open/workspaces/"

export const useWorkspacesStore = defineStore("workspaces", () => {
  const storedWorkspaceId = useCookie("currentWorkspace")

  const contentStore = useContentStore()
  const currentId = ref(storedWorkspaceId)

  const getCurrent = computed(() => {
    return contentStore.getByKey(currentId.value)
  })

  const setCurrentId = (id) => {
    currentId.value = id
    storedWorkspaceId.value = id
  }

  const set = (items) => {
    contentStore.content.value = new Map()
    save(items)
  }

  const save = (items) => {
    contentStore.save(items)
    if (getCurrent.value == null && contentStore.length.value) {
      setCurrentId(items[0].id)
    }
  }

  const remove = (itemId) => {
    contentStore.remove(itemId)
    if (currentId.value === itemId) {
      setCurrentId(
        contentStore.length.value > 0 ? contentStore.getAll.value[0].id : null,
      )
    }
  }

  const getWorkspaceUsers = async() => {
    return await opnFetch(`${workspaceEndpoint}${currentId.value}/users/`)
  }

  const getWorkspaceInvites = async() => {
    return await opnFetch(`${workspaceEndpoint}${currentId.value}/invites/`)
  }

  return {
    ...contentStore,
    currentId,
    getCurrent,
    setCurrentId,
    set,
    save,
    remove,
    getWorkspaceUsers,
    getWorkspaceInvites,
  }
})

export const fetchAllWorkspaces = (options = {}) => {
  return useOpnApi(workspaceEndpoint, options)
}
