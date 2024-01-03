export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  if (!authStore.check) {
    useCookie('intended_url').value = to.path
    return navigateTo({ name: 'login' })
  }
})
