import axios from 'axios'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import Cookies from 'js-cookie'

function addPasswordToFormRequest (request) {
  const url = request.url
  if (!url.startsWith('/api/forms/')) return request

  const slug = url.split('/')[3]
  if (slug !== undefined && slug !== '' && Cookies.get('password-' + slug) !== undefined) {
    request.headers['form-password'] = Cookies.get('password-' + slug)
  }

  return request
}

// Request interceptor
axios.interceptors.request.use(request => {
  const token = store.getters['auth/token']
  if (token) {
    request.headers.common.Authorization = `Bearer ${token}`
  }

  const locale = store.getters['lang/locale']
  if (locale) {
    request.headers.common['Accept-Language'] = locale
  }

  // request.headers['X-Socket-Id'] = Echo.socketId()

  request = addPasswordToFormRequest(request)

  return request
})

// Response interceptor
axios.interceptors.response.use(response => response, error => {
  const { status } = error.response
  if (status >= 500) {
    console.log(status)
  }

  if (status === 401 && store.getters['auth/check']) {
    store.commit('auth/LOGOUT')

    router.push({ name: 'login' })
  }

  return Promise.reject(error)
})
