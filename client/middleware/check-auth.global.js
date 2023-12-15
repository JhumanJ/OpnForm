export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  authStore.loadTokenFromCookie()
  authStore.fetchUserIfNotFetched()
})
