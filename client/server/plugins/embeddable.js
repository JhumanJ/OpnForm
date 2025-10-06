import { getDomain } from '~/lib/utils'

export default defineNitroPlugin((nitroApp) => {
  nitroApp.hooks.hook("render:response", (response, { event }) => {
    const routePath = event.node?.req?.url || event.node?.req?.originalUrl
    const config = useRuntimeConfig()
    
    // Parse allowed domains from private config
    const allowedDomains = config.allowedEmbedDomains
      ? config.allowedEmbedDomains.split(',').map(domain => domain.trim()).filter(Boolean)
      : []

    // Normalize allowed domains to hostnames (supports plain host or full URL)
    const normalizedAllowed = allowedDomains.map((d) => (getDomain(d) || '').toLowerCase()).filter(Boolean)
    
    // Build frame-ancestors for non-form routes: only allowed domains + localhost variants
    const ancestors = ["'self'",
      'http://localhost', 'https://localhost',
      'http://127.0.0.1', 'https://127.0.0.1',
      'http://0.0.0.0', 'https://0.0.0.0',
      'http://[::1]', 'https://[::1]'
    ]
    normalizedAllowed.forEach((host) => {
      ancestors.push(`https://${host}`)
      ancestors.push(`http://${host}`)
    })

    // Remove legacy header to control framing via CSP
    delete response.headers["X-Frame-Options"]
    delete response.headers["x-frame-options"]

    if (routePath && !routePath.startsWith("/forms/")) {
      // Restrict embedding to explicit allowlist + localhost variants
      response.headers["Content-Security-Policy"] = `frame-ancestors ${ancestors.join(' ')};`
    } else {
      // Forms: embeddable anywhere, omit CSP directive
      delete response.headers["Content-Security-Policy"]
    }

    delete response.headers["x-powered-by"]
  })
})
