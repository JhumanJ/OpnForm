import { apiService as api } from './base'

export const templatesApi = {
  // Template operations
  list: (params = {}) => api.get("/templates", { params }),
  get: (slug) => api.get(`/templates/${slug}`),
  create: (data) => api.post("/templates", data),
  update: (id, data) => api.put(`/templates/${id}`, data),
  delete: (id) => api.delete(`/templates/${id}`)
}