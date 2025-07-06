export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated, userData } = useAuthFlow()

  if (isAuthenticated.value && !userData.value?.is_subscribed) {
    return navigateTo({ name: "pricing" })
  }
})
