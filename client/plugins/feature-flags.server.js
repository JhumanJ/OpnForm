import { contentApi } from '~/api/content'

export default defineNuxtPlugin(async () => {
  // Only run on server
  if (import.meta.client) return

  // Initialize state
  const featureFlagsState = useState('featureFlags', () => ({}))

  try {
    // Fetch feature flags fresh from API during SSR
    const flags = await contentApi.featureFlags.list()
    
    // Set the state value
    featureFlagsState.value = flags
    
    console.log('Feature flags loaded during SSR:', flags)
  } catch (error) {
    console.error('Failed to load feature flags during SSR:', error)
    // Provide fallback
    const fallbackFlags = { self_hosted: true, setup_required: false }
    featureFlagsState.value = fallbackFlags
  }
}) 