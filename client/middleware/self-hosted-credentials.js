export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()
  const runtimeConfig = useRuntimeConfig()

  if (useFeatureFlag('self_hosted')) {
    if (authStore.check && authStore.user?.email === 'admin@opnform.com') {
      return navigateTo({ name: "update-credentials" })
    }
  }
})