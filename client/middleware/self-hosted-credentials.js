export default defineNuxtRouteMiddleware(async () => {
  const { isAuthenticated, userData } = useAuthFlow()

  if (useFeatureFlag('self_hosted')) {
    if (isAuthenticated.value && userData.value?.email === 'admin@opnform.com') {
      return navigateTo({ name: "update-credentials" })
    }
  }
})