import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";

export const formsEndpoint = '/open/workspaces/{workspaceId}/forms'
export const singleFormEndpoint = '/open/forms/{slug}'

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
  const loadForm = (slug) => {
    contentStore.startLoading()
    return opnFetch(singleFormEndpoint.replace('{slug}', slug))
      .then(response => {
        contentStore.save(response)
      })
      .finally(() => {
        contentStore.stopLoading()
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

  const publicFetch = (slug) => {
    contentStore.startLoading()
    return opnFetch('/forms/' + slug)
  }

  const allTags = computed(() => {
    let tags = []
    contentStore.getAll.value.forEach((form) => {
      if (form.tags && form.tags.length) {
        if (typeof form.tags === 'string' || form.tags instanceof String ) {
          tags = tags.concat(form.tags.split(','))
        } else if (Array.isArray(form.tags)) {
          tags = tags.concat(form.tags)
        }
      }
    })
    return [...new Set(tags)]
  })

  return {
    ...contentStore,
    allLoaded,
    allTags,
    publicLoad,
    publicFetch,
    loadAll,
    loadForm,
    load,
  }
})
