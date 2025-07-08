export default defineNuxtRouteMiddleware((to) => {
  const { isAuthenticated } = useIsAuthenticated()

  if (!isAuthenticated.value) {
    useCookie("intended_url").value = to.path
    return navigateTo({ name: "login" })
  }
})
