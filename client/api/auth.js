import { apiService } from './base'


export const authApi = {
  // User operations
  user: {
    get: (options) => apiService.get('/user', options),
    delete: () => apiService.delete('/user'),
    updateCredentials: (data) => apiService.post('/update-credentials', data),
    updateProfile: (data) => apiService.patch('/settings/profile', data)
  },

  // Authentication
  login: (data) => apiService.post('/login', data),
  register: (data) => apiService.post('/register', data),
  logout: () => apiService.post('/logout'),

  // OAuth
  oauth: {
    callback: (provider, data) => apiService.post(`/oauth/${provider}/callback`, data)
  }
}