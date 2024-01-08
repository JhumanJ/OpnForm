
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

export function getOpnRequestsOptions(request, opts) {
  opts.headers = {accept: 'application/json', ...opts.headers}

  addAuthHeader(request, opts)
  addPasswordToFormRequest(request, opts)

  const config = useRuntimeConfig()

  console.log('getOpnRequestsOptions', config, request, opts)

  return {
    baseURL: config.public.apiBase,
    onResponseError({response}) {
      const authStore = useAuthStore()
      const {status} = response

      if (status === 401 && authStore.check) {
        console.log("Logging out due to 401")
        authStore.logout()
        useRouter().push({name: 'login'})
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
