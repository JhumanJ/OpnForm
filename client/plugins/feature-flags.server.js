import { contentApi } from '~/api/content'

export default defineNuxtPlugin(async (nuxtApp) => {
  // Only run on server
  if (import.meta.client) return

  try {
    // Fetch feature flags fresh from API during SSR
    const flags = await contentApi.featureFlags.list()
    
    // Inject into payload for client hydration
    nuxtApp.payload.featureFlags = flags
    
    // Make available to server-side code immediately
    nuxtApp.provide('featureFlags', flags)
  } catch (error) {
    console.error('Failed to load feature flags during SSR:', error)
    // Provide fallback
    const fallbackFlags = { self_hosted: true, setup_required: false }
    nuxtApp.payload.featureFlags = fallbackFlags
    nuxtApp.provide('featureFlags', fallbackFlags)
  }
}) 