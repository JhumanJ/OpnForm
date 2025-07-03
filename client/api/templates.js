import { apiService } from './base'

const BASE_PATH = '/templates'

export const templatesApi = {
  // Template operations
  list: (options) => apiService.get(BASE_PATH, options),
  get: (slug, options) => apiService.get(`${BASE_PATH}/${slug}`, options),
  create: (data) => apiService.post(BASE_PATH, data),
  update: (id, data) => apiService.put(`${BASE_PATH}/${id}`, data),
  delete: (id) => apiService.delete(`${BASE_PATH}/${id}`)
}