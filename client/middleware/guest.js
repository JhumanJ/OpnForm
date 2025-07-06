export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useAuthFlow()
  if (isAuthenticated.value) {
    return navigateTo({ name: "home" })
  }
})
