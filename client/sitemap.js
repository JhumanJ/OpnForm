import opnformConfig from "./opnform.config.js";

export default {
  // exclude all URLs that start with /secret
  exclude: ['/settings/**', '/subscriptions/**', '/templates/my-templates'],
  sources: [
    opnformConfig.api_url + '/sitemap-urls'
  ],
  cacheMaxAgeSeconds: 3600
}
