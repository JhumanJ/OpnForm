export default defineNuxtPlugin(async (nuxtApp) => {
  // Load feature flags during SSR
  const featureFlagsState = useState('featureFlags', () => ({}))
  
  try {
    const { contentApi } = await import('~/api/content')
    const flags = await contentApi.featureFlags.list()
    featureFlagsState.value = flags
    console.log('Feature flags loaded on server:', flags)
  } catch (error) {
    console.error('Failed to load feature flags on server:', error)
    // Keep empty object as fallback
  }

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