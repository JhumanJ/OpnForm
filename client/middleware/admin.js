export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()
  if (authStore.check && !authStore.user?.admin) {
    return navigateTo({ name: "home" })
  }
})
