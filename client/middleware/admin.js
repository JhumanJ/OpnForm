export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  if (authStore.check && !authStore.user?.admin) {
    return navigateTo({ name: "home" })
  }
})
