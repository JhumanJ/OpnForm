import { apiService } from './base'

const BASE_PATH = '/moderator'

export const adminApi = {
  // User management
  fetchUser: (identifier, options) => apiService.get(`${BASE_PATH}/fetch-user/${encodeURI(identifier)}`, options),
  impersonate: (userId, options) => apiService.get(`${BASE_PATH}/impersonate/${userId}`, options),

  // Template management
  createTemplate: (data) => apiService.post(`${BASE_PATH}/create-template`, data),

  // Subscription management
  applyDiscount: (data) => apiService.patch(`${BASE_PATH}/apply-discount`, data),
  extendTrial: (data) => apiService.patch(`${BASE_PATH}/extend-trial`, data),
  cancelSubscription: (data) => apiService.patch(`${BASE_PATH}/cancellation-subscription`, data),
  sendPasswordResetEmail: (data) => apiService.patch(`${BASE_PATH}/send-password-reset-email`, data),

  // Billing management
  billing: {
    getEmail: (userId, options) => apiService.get(`${BASE_PATH}/billing/${userId}/email`, options),
    updateEmail: (data) => apiService.patch(`${BASE_PATH}/billing/email`, data),
    getSubscriptions: (userId, options) => apiService.get(`${BASE_PATH}/billing/${userId}/subscriptions`, options),
    getPayments: (userId, options) => apiService.get(`${BASE_PATH}/billing/${userId}/payments`, options)
  },

  // Form management
  forms: {
    getDeleted: (userId, options) => apiService.get(`${BASE_PATH}/forms/${userId}/deleted-forms`, options),
    restore: (slug) => apiService.patch(`${BASE_PATH}/forms/${slug}/restore`)
  }
}