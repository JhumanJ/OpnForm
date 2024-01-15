import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";
import opnformConfig from "~/opnform.config.js";
export const useNotionPagesStore = defineStore('notion_pages', () => {

  const contentStore = useContentStore()

  const load = (pageId) => {
    contentStore.startLoading()

    const apiUrl = opnformConfig.notion.worker
    return useFetch(`${apiUrl}/page/${pageId}`)
      .then(({data, error})=> {
        const val = data.value
        val['id'] = pageId
        contentStore.save(val)
      })
      .finally(() => {
        contentStore.stopLoading()
      })
  }

  return {
    ...contentStore,
    load
  }
})
