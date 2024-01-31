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
    if (!slugs) return []
    return slugs.map((slug) => {
      return types.value.get(slug)
    }).filter((item) => item !== undefined)
  }
  const getTemplateIndustries = (slugs) => {
    if (!slugs) return []
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
    allLoaded,
    getTemplateTypes,
    getTemplateIndustries,
    initTypesAndIndustries
  }
})

export const fetchTemplate = (slug, options = {}) => {
  return useOpnApi(templatesEndpoint + '/' + slug, options)
}

export const fetchAllTemplates = (options = {}) => {
  return useOpnApi(templatesEndpoint, options)
}

export const loadAllTemplates = async (store, options={}) => {
  if (!store.allLoaded) {
    store.startLoading()
    store.initTypesAndIndustries()
    const {data,error} = await fetchAllTemplates(options)
    store.set(data.value)
    store.stopLoading()
    store.allLoaded = true
  }
}
