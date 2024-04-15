export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()
  if (authStore.check && !authStore.user?.moderator) {
    return navigateTo({ name: "home" })
  }
})
