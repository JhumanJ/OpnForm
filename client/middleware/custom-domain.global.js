import {customDomainUsed, getDomain, getHost} from "~/lib/utils.js";

/**
 * Added by Caddy when proxying to the app
 * @type {string}
 */
const customDomainHeaderName = 'CUSTOM_DOMAIN_HEADER'

/**
 * List of routes that can be used with a custom domain
 * @type {string[]}
 */
const customDomainAllowedRoutes = ['forms-slug']

function redirectToMainDomain() {
  return navigateTo(useRuntimeConfig().public.appUrl + '?utm_source=failed_custom_domain_redirect', { redirectCode: 301, external: true })
}

export default defineNuxtRouteMiddleware((to, from) => {
  if (process.client) return

  const config = useRuntimeConfig()

  if (!customDomainUsed()) return

  const customDomainHeaderValue = useRequestHeaders()[customDomainHeaderName]
  if (!customDomainHeaderValue || customDomainHeaderValue !== getDomain(getHost())) {
    // If custom domain header doesn't match, redirect
    return redirectToMainDomain()
  }

  if (!config.public.customDomainsEnabled) {
    // If custom domain not allowed, redirect
    return redirectToMainDomain()
  }

  if (!customDomainAllowedRoutes.includes(to.name)) {
    // Custom domain only allowed for form url
    return redirectToMainDomain()
  }
})

