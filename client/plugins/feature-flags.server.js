export default defineNuxtPlugin(async (nuxtApp) => {
  // Load feature flags during SSR using cached server route
  const featureFlagsState = useState('featureFlags', () => ({}))
  
  try {
    const flags = await $fetch('/api/feature-flags')
    featureFlagsState.value = flags
  } catch (error) {
    console.error('Failed to load feature flags on server:', error)
    // Keep empty object as fallback
  }

  // Provide simple refresh capability
  nuxtApp.provide('refreshFeatureFlags', async () => {
    try {
      // Force fresh fetch by adding cache-busting timestamp
      const flags = await $fetch(`/api/feature-flags?t=${Date.now()}`)
      featureFlagsState.value = flags
      return flags
    } catch (error) {
      console.error('Failed to refresh feature flags:', error)
      throw error
    }
  })
}) 