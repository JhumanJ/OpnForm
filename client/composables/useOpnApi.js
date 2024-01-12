import {getDomain, getHost, customDomainUsed} from "~/lib/utils.js";

function addAuthHeader(request, options) {
  const authStore = useAuthStore()
  if (authStore.token) {
    options.headers = {Authorization: `Bearer ${authStore.token}`, ...options.headers}
  }
}

function addPasswordToFormRequest(request, options) {
  const url = request.url
  if (!url || !url.startsWith('/api/forms/')) return

  const slug = url.split('/')[3]
  const passwordCookie = useCookie('password-' + slug, {maxAge: 60 * 60 * 24 * 30}) // 30 days
  if (slug !== undefined && slug !== '' && passwordCookie.value !== undefined) {
    options.headers['form-password'] = passwordCookie.value
  }
}

/**
 * Add custom domain header if custom domain is used
 */
function addCustomDomainHeader(request, options) {
  if (!customDomainUsed()) return
  options.headers['x-custom-domain'] = getDomain(getHost())
}

export function getOpnRequestsOptions(request, opts) {
  const config = useRuntimeConfig()

  opts.headers = {accept: 'application/json', ...opts.headers}

  // Authenticate requests coming from the server
  if (process.server && config.apiSecret) {
    opts.headers['x-api-secret'] = config.apiSecret
  }

  addAuthHeader(request, opts)
  addPasswordToFormRequest(request, opts)
  addCustomDomainHeader(request, opts)

  return {
    baseURL: config.public.apiBase,
    onResponseError({response}) {
      const authStore = useAuthStore()
      console.log(response)
      const {status} = response

      if (status === 401) {
        if (response.body.error && response.body.error === 'invalid_domain' && process.client) {
          // If invalid domain, redirect to main domain
          window.location.href = config.public.appUrl + '?utm_source=failed_custom_domain_redirect'
          return
        }

        if (authStore.check) {
          console.log("Logging out due to 401")
          authStore.logout()
          useRouter().push({name: 'login'})
        }
      }

      if (status >= 500) {
        console.error('Request error', status)
      }
    },
    ...opts
  }
}

export const opnFetch = (request, opts = {}) => {
  return $fetch(request, getOpnRequestsOptions(request, opts))
}

export const useOpnApi = (request, opts = {}) => {
  return useFetch(request, getOpnRequestsOptions(request, opts))
}
