export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  if (authStore.check && !authStore.user?.moderator) {
    return navigateTo({ name: 'home' })
  }
})
