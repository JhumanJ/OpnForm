export default async (to, from, next) => {
  if (!window.config.custom_domains_enabled) {
    next()
  }
  const isCustomDomain = getDomain(window.location.href) !== getDomain(window.config.app_url)
  if (isCustomDomain && !['forms.show_public'].includes(to.name)) {
    // If route isn't a public form, redirect
    window.location.href = 'https://opnform.com/?utm_source=failed_custom_domain_redirect'
  } else {
    next()
  }
}

function getDomain (url) {
  return (new URL(url)).hostname
}
