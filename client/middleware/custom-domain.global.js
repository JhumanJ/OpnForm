import opnformConfig from "~/opnform.config.js";

function getDomain (url) {
  return (new URL(url)).hostname
}

export default defineNuxtRouteMiddleware((to, from) => {
  if (opnformConfig.custom_domains_enabled && process.client) {
    const isCustomDomain = getDomain(window.location.href) !== getDomain(opnformConfig.app_url)
    if (isCustomDomain && !['forms.show_public'].includes(to.name)) {
      // If route isn't a public form, redirect
      return navigateTo({name: 'home',query: {utm_source: 'failed_custom_domain_redirect'}});
    }
  }
})

