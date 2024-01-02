import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";

export const useNotionPagesStore = defineStore('notion_pages', () => {

  const contentStore = useContentStore()

  const load = (pageId) => {
    contentStore.startLoading()

    const apiUrl = useAppConfig().notion.worker
    return useOpnApi(`${apiUrl}/page/${pageId}`)
      .then(({data})=> {
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
