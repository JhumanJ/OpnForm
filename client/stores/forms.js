import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"
import { formsApi } from "~/api"

export const formsEndpoint = "/open/workspaces/{workspaceId}/forms"
export const singleFormEndpoint = "/open/forms/{slug}"

export const useFormsStore = defineStore("forms", () => {
  const contentStore = useContentStore("slug")
  const allLoaded = ref(false)
  const loadAllRequest = ref(null)

  const loadAll = (workspaceId) => {
    if (loadAllRequest.value) {
      return loadAllRequest.value
    }
    if (!workspaceId) {
      return
    }
    contentStore.startLoading()
    
    loadAllRequest.value = new Promise((resolve, reject) => {
      formsApi.list(workspaceId, { query: { page: 1 } })
        .then(firstResponse => {
          contentStore.resetState()
          contentStore.save(firstResponse.data)
          
          const lastPage = firstResponse.meta.last_page
          
          if (lastPage > 1) {
            // Create an array of promises for remaining pages
            const remainingPages = Array.from({ length: lastPage - 1 }, (_, i) => {
              const page = i + 2 // Start from page 2
              return formsApi.list(workspaceId, { query: { page } })
            })
            
            // Fetch all remaining pages in parallel
            return Promise.all(remainingPages)
          }
          return []
        })
        .then(responses => {
          // Save all responses data
          responses.forEach(response => {
            contentStore.save(response.data)
          })
          
          allLoaded.value = true
          contentStore.stopLoading()
          loadAllRequest.value = null
          resolve()
        })
        .catch(err => {
          contentStore.stopLoading()
          loadAllRequest.value = null
          reject(err)
        })
    })

    return loadAllRequest.value
  }

  const loadForm = (slug) => {
    contentStore.startLoading()
    return formsApi.get(slug)
      .then((response) => {
        contentStore.save(response)
      })
      .finally(() => {
        contentStore.stopLoading()
      })
  }

  const load = (workspaceId, slug) => {
    contentStore.startLoading()
    return formsApi.getById(slug)
    .finally(() => {
      contentStore.stopLoading()
    })
  }

  /**
   * Load a form from the public API
   */
  const publicLoad = (slug) => {
    contentStore.startLoading()
    return useOpnApi("/forms/" + slug)
  }

  const publicFetch = (slug) => {
    contentStore.startLoading()
    return formsApi.get(slug, { server: false })
  }

  const allTags = computed(() => {
    let tags = []
    contentStore.getAll.value.forEach((form) => {
      if (form.tags && form.tags.length) {
        if (typeof form.tags === "string" || form.tags instanceof String) {
          tags = tags.concat(form.tags.split(","))
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
