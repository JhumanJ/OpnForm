export default defineNuxtRouteMiddleware(() => {
  if (!useFeatureFlag('self_hosted')) return
  const redirectUrl = useRuntimeConfig().public.rootRedirectUrl

  // Only run if env var is set and is a valid URL
  if (!redirectUrl || !/^https?:\/\//.test(redirectUrl)) return

  if (import.meta.server) {
    return navigateTo(redirectUrl, { redirectCode: 301 })
  } else {
    window.location.replace(redirectUrl)
  }
}) 