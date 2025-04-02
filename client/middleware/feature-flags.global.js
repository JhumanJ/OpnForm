import { useFeatureFlagsStore } from '~/stores/featureFlags'

export default defineNuxtRouteMiddleware(async () => {
  const featureFlagsStore = useFeatureFlagsStore()

  // Load flags if they haven't been loaded yet
  if (!featureFlagsStore.isLoaded) {
    await featureFlagsStore.fetchFlags()
  }
})