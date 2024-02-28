import { defineStore } from 'pinia'
import { useContentStore } from "~/composables/stores/useContentStore.js";

export const integrationsEndpoint = '/open/forms/{id}/integrations'

export const useFormIntegrationsStore = defineStore('form_integrations', () => {

  const contentStore = useContentStore()

  const fetchIntegrations = (formId) => {
    contentStore.resetState()
    contentStore.startLoading()
    return useOpnApi(integrationsEndpoint.replace('{id}', formId)).then((response) => {
      contentStore.save(response.data.value)
      contentStore.stopLoading()
    })
  }

  const getAllByFormId = (formId) => {
    return contentStore.getAll.value.filter((item) => {
      return (item.form_id) ? item.form_id === formId : false
    })
  }

  return {
    ...contentStore,
    fetchIntegrations,
    getAllByFormId
  }
})
