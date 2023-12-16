export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  if (!authStore.check) {
    useCookie('intended_url').value = to.path

    console.log('redirecting to login')
    return navigateTo({ name: 'login' })
  }
})
