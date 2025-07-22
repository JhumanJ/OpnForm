export default defineNuxtPlugin((nuxtApp) => {
  // Access the same state that was set on server
  const featureFlagsState = useState('featureFlags', () => ({}))
    
  // Provide refresh capability
  nuxtApp.provide('refreshFeatureFlags', async () => {
    try {
      const { contentApi } = await import('~/api/content')
      const newFlags = await contentApi.featureFlags.list()
      featureFlagsState.value = newFlags
      return newFlags
    } catch (error) {
      console.error('Failed to refresh feature flags:', error)
      throw error
    }
  })
}) 