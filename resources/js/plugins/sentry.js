import Vue from 'vue'
import * as Sentry from '@sentry/vue'

if (window.config.sentry_dsn) {
  Sentry.init({
    Vue,
    dsn: window.config.sentry_dsn,
    integrations: [],
    // Performance Monitoring
    tracesSampleRate: 0.01,
    logErrors: true,
    debug: false
  })
  if (!window.config.production) {
    console.info('== Sentry enabled ==')
  }
}
