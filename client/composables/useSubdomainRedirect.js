import { sendRedirect } from 'h3'

/**
 * Composable for handling subdomain redirects
 * Used to redirect users to a configured URL when accessing certain pages
 * 
 * @returns {Object} - Object with redirect functions
 */
export const useSubdomainRedirect = () => {
  /**
   * Check if subdomain redirect is enabled and configured
   * @returns {boolean} - Whether redirect should happen
   */
  const shouldRedirect = () => {
    if (!useFeatureFlag('self_hosted')) return false
    
    const redirectUrl = useRuntimeConfig().public.rootRedirectUrl
    return redirectUrl && /^https?:\/\//.test(redirectUrl)
  }

  /**
   * Get the configured redirect URL
   * @returns {string|null} - The redirect URL or null if not configured
   */
  const getRedirectUrl = () => {
    if (!shouldRedirect()) return null
    return useRuntimeConfig().public.rootRedirectUrl
  }

  /**
   * Perform SSR redirect if possible, otherwise client-side redirect
   * @param {Object} options - Options for the redirect
   * @param {boolean} options.skipIfIframe - Skip redirect if in iframe (default: true)
   * @returns {Promise<void>} - Promise that resolves when redirect is complete
   */
  const performRedirect = async (options = {}) => {
    const { skipIfIframe = true } = options
    
    if (!shouldRedirect()) return
    
    const redirectUrl = getRedirectUrl()
    if (!redirectUrl) return
    
    // Skip redirect if in iframe and option is enabled
    if (skipIfIframe && import.meta.client && useIsIframe().value) {
      return
    }

    // Server-side redirect (SSR)
    if (import.meta.server) {
      const event = useRequestEvent()
      if (event) {
        await sendRedirect(event, redirectUrl, 301)
        return
      }
    }
    
    // Client-side redirect fallback
    if (import.meta.client) {
      await navigateTo(redirectUrl, { external: true })
    }
  }

  return {
    shouldRedirect,
    getRedirectUrl,
    performRedirect
  }
} 