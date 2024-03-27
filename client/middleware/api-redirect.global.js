
export default defineNuxtRouteMiddleware((to, from) => {
  if (import.meta.client) return

  const config = useRuntimeConfig()
  if (to.fullPath.startsWith('/api')) {
    const path = to.fullPath.replace('/api', '')
    return navigateTo(config.public.apiBase + path, { redirectCode: 301, external: true })
  }
})

