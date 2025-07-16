export default defineNuxtRouteMiddleware(() => {
  const { isAuthenticated } = useIsAuthenticated()
  const { user } = useAuth()
  const { data: userData } = user()

  if (isAuthenticated.value && !userData.value?.is_subscribed) {
    throw createError({ statusCode: 403, statusMessage: 'Subscription required' })
  }
})
