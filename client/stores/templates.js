import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";
import templateTypes from "~/data/forms/templates/types.json"
import industryTypes from "~/data/forms/templates/industries.json"

const templatesEndpoint = 'templates'
export const useTemplatesStore = defineStore('templates', () => {

  const contentStore = useContentStore('slug')

  const allLoaded = ref(false)
  const industries = ref(new Map)
  const types = ref(new Map)

  const getTemplateTypes = (slugs) => {
    return slugs.map((slug) => {
      return types.value.get(slug)
    }).filter((item) => item !== undefined)
  }
  const getTemplateIndustries = (slugs) => {
    return slugs.map((slug) => {
      return industries.value.get(slug)
    }).filter((item) => item !== undefined)
  }

  const initTypesAndIndustries = () => {
    if (types.value.size === 0) {
      types.value = new Map(Object.entries(templateTypes))
    }
    if (industries.value.size === 0) {
      industries.value = new Map(Object.entries(industryTypes))
    }
  }

  return {
    ...contentStore,
    industries,
    types,
    getTemplateTypes,
    getTemplateIndustries,
    initTypesAndIndustries
  }
})

export const fetchTemplate = (slug) => {
  return useOpnApi(templatesEndpoint + '/' + slug)
}

export const fetchAllTemplates = () => {
  return useOpnApi(templatesEndpoint)
}

export const loadAllTemplates = async (store) => {
  if (!store.allLoaded) {
    store.startLoading()
    store.initTypesAndIndustries()
    const {data} = await fetchAllTemplates()
    store.set(data.value)
    store.allLoaded = true
  }
}
