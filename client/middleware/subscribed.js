export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()

  if (authStore.check && !authStore.user?.is_subscribed) {
    return navigateTo({ name: "pricing" })
  }
})
