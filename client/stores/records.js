import { defineStore } from 'pinia'
import { useContentStore } from '~/composables/stores/useContentStore'

/**
 * Loads records from database
 */
export const useRecordsStore = defineStore('records', ()=>{

  const contentStore = useContentStore()

  const loadRecord = (request)=> {
    contentStore.resetState()
    contentStore.startLoading()
    return request.then((data) => {
      contentStore.save(data)
      contentStore.stopLoading()
    })
  }
  return {...contentStore, loadRecord}

})
