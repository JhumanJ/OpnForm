export default defineNuxtRouteMiddleware(async () => {
  const { performRedirect } = useSubdomainRedirect()
  
  // Perform redirect with iframe check disabled for root pages
  // (root pages should always redirect regardless of iframe context)
  await performRedirect({ skipIfIframe: false })
}) 