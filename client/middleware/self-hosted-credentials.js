export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (useFeatureFlag('self_hosted')) {
    if (authStore.check && authStore.user?.email === 'admin@opnform.com') {
      return navigateTo({ name: "update-credentials" })
    }
  }
})