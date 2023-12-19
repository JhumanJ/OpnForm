import {defineStore} from 'pinia'
import {useContentStore} from "~/composables/stores/useContentStore.js";

const templatesEndpoint = 'templates'
export const useTemplatesStore = defineStore('templates', () => {

  const contentStore = useContentStore('slug')

  const allLoaded = ref(false)
  const industries = ref({})
  const types = ref({})

  const getTemplateTypes = computed((state) => (slugs) => {
    if (state.types.length === 0) return null
    return Object.values(state.types).filter((val) => slugs.includes(val.slug)).map((item) => {
      return item.name
    })
    // todo: use map
  })
  const getTemplateIndustries = computed((state) => (slugs) => {
    if (state.industries.length === 0) return null
    return Object.values(state.industries).filter((val) => slugs.includes(val.slug)).map((item) => {
      return item.name
    })
  })

  const loadTypesAndIndustries = function() {
    if (Object.keys(this.industries).length === 0 || Object.keys(this.types).length === 0) {
      // const files = import.meta.glob('~/data/forms/templates/*.json')
      // console.log(await files['/data/forms/templates/industries.json']())
      // this.industries = await files['/data/forms/templates/industries.json']()
      // this.types = await files['/data/forms/templates/types.json']()
    }
  }

  return {
    ...contentStore,
    industries,
    types,
    getTemplateTypes,
    getTemplateIndustries,
    loadTypesAndIndustries,
  }
})

export const fetchTemplate = (slug) => {
  return useOpnApi(templatesEndpoint + '/' + slug)
}

export const fetchAllTemplates = () => {
  return useOpnApi(templatesEndpoint)
}

