import {defineStore} from 'pinia'
import {useStorage} from "@vueuse/core"
import {useContentStore} from "~/composables/stores/useContentStore.js";

export const workspaceEndpoint = 'open/workspaces/'

export const useWorkspacesStore = defineStore('workspaces', () => {

  const storedWorkspaceId = useCookie('currentWorkspace')

  const contentStore = useContentStore()
  const currentId = ref(storedWorkspaceId)

  const getCurrent = computed(() => {
    return contentStore.getByKey(currentId.value)
  })

  const setCurrentId = (id) => {
    currentId.value = id
    storedWorkspaceId.value = id
  }

  const save = (items) => {
    contentStore.save(items)
    if ((getCurrent.value == null) && contentStore.length.value) {
      setCurrentId(items[0].id)
    }
  }

  const remove = (itemId) => {
    contentStore.remove(itemId)
    if (currentId.value === itemId) {
      setCurrentId(contentStore.length.value > 0 ? contentStore.getAll[0].id : null)
    }
  }

  return {
    ...contentStore,
    currentId,
    getCurrent,
    setCurrentId,
    save,
    remove
  }
})

export const fetchAllWorkspaces = (options = {}) => {
  return useOpnApi(workspaceEndpoint, options)
}
