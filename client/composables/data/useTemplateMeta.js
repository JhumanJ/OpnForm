import templateTypes from "~/data/forms/templates/types.json"
import industryTypes from "~/data/forms/templates/industries.json"

const typesMap = new Map(Object.entries(templateTypes))
const industriesMap = new Map(Object.entries(industryTypes))

export function useTemplateMeta() {
  const getTemplateTypes = (slugs) => {
    if (!slugs) return []
    return slugs
      .map((slug) => typesMap.get(slug))
      .filter((item) => item !== undefined)
  }

  const getTemplateIndustries = (slugs) => {
    if (!slugs) return []
    return slugs
      .map((slug) => industriesMap.get(slug))
      .filter((item) => item !== undefined)
  }

  return {
    types: typesMap,
    industries: industriesMap,
    getTemplateTypes,
    getTemplateIndustries,
  }
} 