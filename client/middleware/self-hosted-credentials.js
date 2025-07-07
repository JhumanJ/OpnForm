export default defineNuxtRouteMiddleware(async () => {
  const { isAuthenticated } = useAuthFlow()
  const { user } = useAuth()
  const { data: userData } = user()

  if (useFeatureFlag('self_hosted')) {
    if (isAuthenticated.value && userData.value?.email === 'admin@opnform.com') {
      throw createError({ statusCode: 403, statusMessage: 'Please update your credentials' })
    }
  }
})