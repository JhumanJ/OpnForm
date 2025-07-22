export default defineNuxtPlugin((nuxtApp) => {
  // Get flags from SSR payload
  const ssrFlags = nuxtApp.payload?.featureFlags || {}
  
  // Initialize client-side reactive state
  const featureFlags = useState('featureFlags', () => ssrFlags)
  
  // Provide refresh capability
  nuxtApp.provide('refreshFeatureFlags', async () => {
    try {
      const { contentApi } = await import('~/api/content')
      const newFlags = await contentApi.featureFlags.list()
      featureFlags.value = newFlags
      return newFlags
    } catch (error) {
      console.error('Failed to refresh feature flags:', error)
      throw error
    }
  })
}) 