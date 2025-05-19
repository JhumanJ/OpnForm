import gtmConfig from '../gtm'

export default defineNuxtPlugin(() => {
  const route = useRoute()
  const isIframe = useIsIframe()
  const isPublicFormPage = route.name === 'forms-slug'
  
  // Only enable GTM if not in a form page (for respondents) and not in an iframe
  if (!isPublicFormPage && !isIframe && process.env.NUXT_PUBLIC_GTM_CODE) {
    // Initialize GTM manually only when needed
    const gtm = useGtm()
    
    // Override the enabled setting to true for this session
    gtmConfig.enabled = true
    
    // Watch for route changes to track page views
    watch(() => route.fullPath, () => {
      if (!route.name || route.name !== 'forms-slug') {
        gtm.trackView(route.name, route.fullPath)
      }
    }, { immediate: true })
    
    return {
      provide: {
        gtm
      }
    }
  }
  
  return {}
}) 