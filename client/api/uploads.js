import { apiService } from './base'

export const uploadsApi = {
  // File upload operations
  uploadFile: (data, options) => apiService.post('/upload-file', data, options),
  
  // Vapor signed URL (if using Laravel Vapor)
  getSignedStorageUrl: (data) => apiService.post('/vapor/signed-storage-url', data)
}