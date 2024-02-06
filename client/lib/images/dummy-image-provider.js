import { joinURL } from 'ufo'
import { createOperationsGenerator } from '#image'

const operationsGenerator = createOperationsGenerator()

export const getImage = (
  src,
  { modifiers = {}, baseURL } = {}
) => {

  if (!baseURL) {
    // also support runtime config
    baseURL = useRuntimeConfig().public.siteUrl
  }

  const operations = operationsGenerator(modifiers)

  return {
    url: joinURL(baseURL, src + (operations ? '?' + operations : '')),
  }
}
