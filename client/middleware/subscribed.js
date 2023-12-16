export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  if (authStore.check && !authStore.user?.is_subscribed) {
    return navigateTo({ name: 'pricing' })
  }
})
