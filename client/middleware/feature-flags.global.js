import { useFeatureFlagsStore } from '~/stores/featureFlags'

export default defineNuxtRouteMiddleware(async () => {
  const featureFlagsStore = useFeatureFlagsStore()

  if (import.meta.server && Object.keys(featureFlagsStore.flags).length === 0) {
    await featureFlagsStore.fetchFlags()
  }
})