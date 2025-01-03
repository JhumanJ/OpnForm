import * as Sentry from '@sentry/nuxt'

// Only run `init` when process.env.SENTRY_DSN is available.
if (process.env.NUXT_PUBLIC_SENTRY_DSN) {
  Sentry.init({
    dsn: process.env.NUXT_PUBLIC_SENTRY_DSN,
  })
}
