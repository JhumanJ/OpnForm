import axios from 'axios'
import { useAuthStore } from '../stores/auth';
import router from '~/router'
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
  const authStore = useAuthStore()
  const token = authStore.token
  if (token) {
    request.headers.common.Authorization = `Bearer ${token}`
  }

  request.headers.common['Accept-Language'] = 'en-US'
  // request.headers['X-Socket-Id'] = Echo.socketId()

  request = addPasswordToFormRequest(request)

  return request
})

// Response interceptor
axios.interceptors.response.use(response => response, error => {
  const authStore = useAuthStore()
  const { status } = error.response
  if (status >= 500) {
    console.log(status)
  }

  if (status === 401 && authStore.check) {
    authStore.logout()
    router.push({ name: 'login' })
  }

  return Promise.reject(error)
})
