import { apiService } from './base'

const BASE_PATH = '/settings/providers'

export const oauthApi = {
  // Provider operations
  list: (options) => apiService.get('/open/providers', options),
  connect: (service, data) => apiService.post(`${BASE_PATH}/connect/${service}`, data),
  callback: (service, data) => apiService.post(`${BASE_PATH}/callback/${service}`, data),
  widgetCallback: (service, data) => apiService.post(`${BASE_PATH}/widget-callback/${service}`, data),
  delete: (providerId) => apiService.delete(`${BASE_PATH}/${providerId}`),

  // OAuth flow
  redirect: (provider, data) => apiService.post(`/oauth/connect/${provider}`, data)
}