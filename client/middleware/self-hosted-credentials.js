export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()
  const featureFlagsStore = useFeatureFlagsStore()

  // Ensure feature flags are loaded
  if (!featureFlagsStore.isLoaded) {
    await featureFlagsStore.fetchFlags()
  }

  if (useFeatureFlag('self_hosted')) {
    if (authStore.check && authStore.user?.email === 'admin@opnform.com') {
      return navigateTo({ name: "update-credentials" })
    }
  }
})