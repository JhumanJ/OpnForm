import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";

export const formsEndpoint = '/open/workspaces/{workspaceId}/forms'

export const useFormsStore = defineStore('forms', () => {

  const contentStore = useContentStore('slug')
  contentStore.startLoading()
  const currentPage = ref(1)

  const load = (workspaceId) => {
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
          load(workspaceId)
        } else {
          contentStore.stopLoading()
          currentPage.value = 1
        }
      })
  }

  return {
    ...contentStore,
    load
  }
})
