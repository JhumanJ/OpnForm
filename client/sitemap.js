export default {
  exclude: ['/settings/**', '/subscriptions/**', '/templates/my-templates'],
  sources: [
    process.env.NUXT_PUBLIC_API_BASE + '/sitemap-urls'
  ],
  cacheMaxAgeSeconds: 3600
}
