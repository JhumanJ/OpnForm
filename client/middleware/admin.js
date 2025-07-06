export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated, userData } = useAuthFlow()
  if (isAuthenticated.value && !userData.value?.admin) {
    return navigateTo({ name: "home" })
  }
})
