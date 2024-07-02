export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()
  if (authStore.check) {
    return navigateTo({ name: "home" })
  }
})
