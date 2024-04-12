export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()

  if (!authStore.check) {
    useCookie("intended_url").value = to.path
    return navigateTo({ name: "login" })
  }
})
