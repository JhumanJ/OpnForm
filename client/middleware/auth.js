export default defineNuxtRouteMiddleware((to) => {
  const { isAuthenticated } = useAuthFlow()

  if (!isAuthenticated.value) {
    useCookie("intended_url").value = to.path
    return navigateTo({ name: "login" })
  }
})
