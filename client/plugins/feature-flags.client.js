export default defineNuxtPlugin((nuxtApp) => {
  // Access the same state that was set on server
  const featureFlagsState = useState('featureFlags', () => ({}))
  
  console.log('Feature flags hydrated on client:', featureFlagsState.value)
  
  // Provide refresh capability
  nuxtApp.provide('refreshFeatureFlags', async () => {
    try {
      const { contentApi } = await import('~/api/content')
      const newFlags = await contentApi.featureFlags.list()
      featureFlagsState.value = newFlags
      console.log('Feature flags refreshed:', newFlags)
      return newFlags
    } catch (error) {
      console.error('Failed to refresh feature flags:', error)
      throw error
    }
  })
}) 