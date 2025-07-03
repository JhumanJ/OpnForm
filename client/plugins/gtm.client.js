export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig()
  const gtmId = config.public.gtmCode

  const route = useRoute()
  const isIframe = useIsIframe()
  const isPublicFormPage = route.name === 'forms-slug'
  
  console.log('gtmId gtmId', gtmId)
  const gtm = useGtm()

  // Only enable GTM if not in a form page (for respondents) and not in an iframe
  if (gtmId && gtmId !== 'GTM-XXXXXX' && gtm && !isPublicFormPage && !isIframe) {
    
    // Override the enabled setting to true for this session
    gtm.enable(true)
        
    // Initialize Consent Mode
    window.dataLayer = window.dataLayer || []
    function gtag() {
      window.dataLayer.push(arguments)
    }
    
    gtag('consent', 'default', {
      'ad_storage': 'denied',
      'analytics_storage': 'granted',
      'functionality_storage': 'granted',
      'security_storage': 'granted'
    })
    
    // Enable IP anonymization
    gtag('set', 'anonymize_ip', true)
    
  }
  
  return {}
}) 