import { defineStore } from 'pinia'
import { useContentStore } from "~/composables/stores/useContentStore.js";
import integrationsList from '~/data/forms/integrations.json'

export const formIntegrationsEndpoint = '/open/forms/{id}/integrations'

export const useFormIntegrationsStore = defineStore('form_integrations', () => {

  const contentStore = useContentStore()
  const integrations = ref(new Map)

  const initIntegrations = () => {
    if (integrations.value.size === 0) {
      integrations.value = new Map(Object.entries(integrationsList))
    }
  }

  const integrationsBySection = computed(() => {
    const groupedObject = {};
    for (const [key, integration] of integrations.value) {
      const sectionName = integration.section_name;
      if (!groupedObject[sectionName]) {
        groupedObject[sectionName] = {};
      }
      groupedObject[sectionName][key] = integration
    }
    return groupedObject;
  })

  const fetchFormIntegrations = (formId) => {
    contentStore.resetState()
    contentStore.startLoading()
    return useOpnApi(formIntegrationsEndpoint.replace('{id}', formId)).then((response) => {
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
    integrations,
    initIntegrations,
    integrationsBySection,
    fetchFormIntegrations,
    getAllByFormId,
  }
})
