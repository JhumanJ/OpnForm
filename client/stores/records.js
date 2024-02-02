import { defineStore } from 'pinia'
import { useContentStore } from '~/composables/stores/useContentStore'

/**
 * Loads records from database
 */
export const useRecordsStore = defineStore('records', ()=>{

  const contentStore = useContentStore()

  const loadRecord = (request)=> {
    this.contentStore.resetState()
    this.contentStore.startLoading()
    return request.then((data) => {
      this.contentStore.save(data)
      this.contentStore.stopLoading()
    })
  }
  return {...contentStore, loadRecord}

})