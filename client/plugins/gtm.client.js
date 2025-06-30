export default defineNuxtPlugin(() => {
  const route = useRoute()
  const isIframe = useIsIframe()
  const isPublicFormPage = route.name === 'forms-slug'
  
  // Only enable GTM if not in a form page (for respondents) and not in an iframe
  if (!isPublicFormPage && !isIframe) {
    // Initialize GTM manually only when needed
    const gtm = useGtm()
    
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