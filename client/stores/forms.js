import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";

export const formsEndpoint = '/open/workspaces/{workspaceId}/forms'

export const useFormsStore = defineStore('forms', () => {

  const contentStore = useContentStore('slug')
  const allLoaded = ref(false)
  const currentPage = ref(1)

  const loadAll = (workspaceId) => {
    contentStore.startLoading()
    return opnFetch(formsEndpoint.replace('{workspaceId}', workspaceId),{query: {page: currentPage.value}})
      .then((response) => {
        if (currentPage.value === 1) {
          contentStore.resetState()
          contentStore.save(response.data)
        } else {
          contentStore.save(response.data)
        }
        if (currentPage.value < response.meta.last_page) {
          currentPage.value++
          loadAll(workspaceId)
        } else {
          allLoaded.value = true
          contentStore.stopLoading()
          currentPage.value = 1
        }
      }).catch((error) => {
        contentStore.stopLoading()
        currentPage.value = 1
        throw error
      })
  }

  const load = (workspaceId, slug) => {
    contentStore.startLoading()
    return opnFetch(formsEndpoint.replace('{workspaceId}', workspaceId) + '/' + slug)
      .finally(() => {
        contentStore.stopLoading()
      })
  }

  /**
   * Load a form from the public API
   */
  const publicLoad = (slug) => {
    contentStore.startLoading()
    return useOpnApi('/forms/' + slug)
  }

  const allTags = computed(() => {
    let tags = []
    contentStore.getAll.value.forEach((form) => {
      if (form.tags && form.tags.length) {
        tags = tags.concat(form.tags.split(','))
      }
    })
    return [...new Set(tags)]
  })

  return {
    ...contentStore,
    allLoaded,
    allTags,
    publicLoad,
    loadAll,
    load,
  }
})
