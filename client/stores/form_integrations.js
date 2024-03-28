import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";
import integrationsList from '~/data/forms/integrations.json'

export const formIntegrationsEndpoint = '/open/forms/{formid}/integrations'

export const useFormIntegrationsStore = defineStore('form_integrations', () => {

  const contentStore = useContentStore()
  const integrations = ref(new Map)

  const availableIntegrations = computed(() => {
    const user = useAuthStore().user
    if (!user) return integrations.value

    const enrichedIntegrations = new Map()
    for (const [key, integration] of integrations.value.entries()) {
      enrichedIntegrations.set(key, {
        ...integration,
        id: key,
        requires_subscription: !user.is_subscribed && integration.is_pro
      })
    }

    return enrichedIntegrations
  })

  const integrationsBySection = computed(() => {
    const groupedObject = {};
    for (const [key, integration] of availableIntegrations.value.entries()) {
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
    return useOpnApi(formIntegrationsEndpoint.replace('{formid}', formId)).then((response) => {
      contentStore.save(response.data.value)
      contentStore.stopLoading()
    })
  }

  const getAllByFormId = (formId) => {
    return contentStore.getAll.value.filter((item) => {
      return (item.form_id) ? item.form_id === formId : false
    })
  }

  const initIntegrations = () => {
    if (integrations.value.size === 0) {
      integrations.value = new Map(Object.entries(integrationsList))
    }
  }

  initIntegrations()

  return {
    ...contentStore,
    initIntegrations,
    availableIntegrations,
    integrationsBySection,
    fetchFormIntegrations,
    getAllByFormId,
  }
})
