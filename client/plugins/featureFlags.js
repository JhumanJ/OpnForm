import { useFeatureFlagsStore } from '~/stores/featureFlags'

export default defineNuxtPlugin(async (nuxtApp) => {
  // Get the pinia instance for SSR compatibility
  const { $pinia } = nuxtApp

  try {    
    // Pass pinia instance for SSR compatibility
    const featureFlagsStore = useFeatureFlagsStore($pinia)
    
    // Fetch flags during SSR to prevent hydration mismatches
    if (!featureFlagsStore.isLoaded) {
      await featureFlagsStore.fetchFlags()
    }
  } catch (error) {
    console.error('Feature flags plugin failed:', error)
  }
})