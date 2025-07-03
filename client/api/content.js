import { apiService } from './base'

export const contentApi = {
  // Changelog
  changelog: {
    getEntries: (options) => apiService.get('/content/changelog/entries', options)
  },

  // Fonts
  fonts: {
    list: (options) => apiService.get('/fonts', options)
  },

  // Feature flags
  featureFlags: {
    list: (options) => apiService.get('/content/feature-flags', options)
  },

  // Sitemap
  sitemap: {
    getUrls: (options) => apiService.get('/sitemap-urls', options)
  }
}