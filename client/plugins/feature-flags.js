import { useFeatureFlagsStore } from '~/stores/featureFlags'

export default defineNuxtPlugin(async () => {
  const featureFlagsStore = useFeatureFlagsStore()

  // Load flags if they haven't been loaded yet
  if (!featureFlagsStore.isLoaded) {
    await featureFlagsStore.fetchFlags()
  }
}) 