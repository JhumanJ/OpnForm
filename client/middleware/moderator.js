export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated, userData } = useAuthFlow()
  if (isAuthenticated.value && !userData.value?.moderator) {
    return navigateTo({ name: "home" })
  }
})
