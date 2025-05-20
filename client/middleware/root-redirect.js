import { sendRedirect } from 'h3'

export default defineNuxtRouteMiddleware(() => {
  if (!useFeatureFlag('self_hosted')) return
  const redirectUrl = useRuntimeConfig().public.rootRedirectUrl

  // Only run if env var is set and is a valid URL
  if (!redirectUrl || !/^https?:\/\//.test(redirectUrl)) return

  // Server-side: use h3's sendRedirect
  if (import.meta.server) {

    const event = useRequestEvent()
    if (event) {
      return sendRedirect(event, redirectUrl, 301)
    }
  }
  
  // Client-side handling
  return navigateTo(redirectUrl, { external: true })
}) 