import { ofetch } from 'ofetch'
import {useAuthStore} from "~/stores/auth.js";

function addAuthHeader(request, options) {
  const authStore = useAuthStore()
  if (authStore.check) {
    options.headers = { Authorization: `Bearer ${authStore.token}` }
    console.log('addidng auth',options)
  }
}

function addPasswordToFormRequest (request) {
  const url = request.url
  if (!url || !url.startsWith('/api/forms/')) return

  const slug = url.split('/')[3]
  const passwordCookie = useCookie('password-' + slug, { maxAge: 60 * 60 * 24 * 30 }) // 30 days
  if (slug !== undefined && slug !== '' && passwordCookie.value !== undefined) {
    request.headers['form-password'] = passwordCookie.value
  }
}

export default defineNuxtPlugin((_nuxtApp) => {
  globalThis.$fetch = ofetch.create({
    onRequest ({ request, options }) {
      // TODO: check that it's our own domain called
      addAuthHeader(request, options)
      addPasswordToFormRequest(request)
    },
    onResponseError ({ response }) {
      const authStore = useAuthStore()
      const { status } = response

      if (status === 401 && authStore.check) {
        // TODO: check that it's our own domain called
        authStore.logout()
        useRouter().push({ name: 'login' })
      }

      if (status >= 500) {
        console.error('Request error', status)
      }
    }
  })
})
