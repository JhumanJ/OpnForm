import {customDomainUsed, getDomain, getHost} from "~/lib/utils.js"

/**
 * Added by Caddy when proxying to the app
 * @type {string}
 */
const customDomainHeaderName = "user-custom-domain"

/**
 * List of routes that can be used with a custom domain
 * @type {string[]}
 */
const customDomainAllowedRoutes = ["forms-slug"]

function redirectToMainDomain(details = {}) {
  const config = useRuntimeConfig()
  const appDomain = getDomain(config.public.appUrl)
  const host = getHost()
  console.warn("Redirecting to main domain", {
    reason: "unknown",
    appDomain,
    host,
    getDomain: getDomain(host), ...details
  })
  return navigateTo(
    useRuntimeConfig().public.appUrl +
    "?utm_source=failed_custom_domain_redirect",
    {redirectCode: 301, external: true},
  )
}

export default defineNuxtRouteMiddleware((to) => {
  if (!customDomainUsed()) return

  const customDomainHeaderValue = useRequestHeaders()[customDomainHeaderName]
  if (
    import.meta.server &&
    (!customDomainHeaderValue ||
      customDomainHeaderValue !== getDomain(getHost()))
  ) {
    return redirectToMainDomain({
      reason: "header_mismatch",
      customDomainHeaderValue: customDomainHeaderValue,
      host: getDomain(getHost()),
    })
  }

  if (!useFeatureFlag('custom_domains')) {
    // If custom domain not allowed, redirect
    return redirectToMainDomain({
      reason: "custom_domains_disabled",
    })
  }

  if (!customDomainAllowedRoutes.includes(to.name)) {
    // Custom domain only allowed for form url
    return redirectToMainDomain({
      reason: "route_not_allowed",
      route: to.name
    })
  }
})
