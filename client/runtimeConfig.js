function parseBoolean(value, defaultValue = false) {
  if (typeof value === 'string') {
    value = value.toLowerCase().trim()
    if (value === 'true' || value === '1') return true
    if (value === 'false' || value === '0') return false
  }
  return defaultValue
}

function parseNumber(value, defaultValue = 0) {
  const parsedValue = parseFloat(value)
  return isNaN(parsedValue) ? defaultValue : parsedValue
}

export default {
  // Keys within public, will be also exposed to the client-side
  public: {
    apiBase: process.env.NUXT_PUBLIC_API_BASE || '',
    appUrl: process.env.NUXT_PUBLIC_APP_URL || '',
    env: process.env.NUXT_PUBLIC_ENV || 'local',
    hCaptchaSiteKey: process.env.NUXT_PUBLIC_H_CAPTCHA_SITE_KEY || null,
    gtmCode: process.env.NUXT_PUBLIC_GTM_CODE || null,
    amplitudeCode: process.env.NUXT_PUBLIC_AMPLITUDE_CODE || null,
    crispWebsiteId: process.env.NUXT_PUBLIC_CRISP_WEBSITE_ID || null,
    aiFeaturesEnabled: parseBoolean(process.env.NUXT_PUBLIC_AI_FEATURES_ENABLED),
    s3Enabled: parseBoolean(process.env.NUXT_PUBLIC_S3_ENABLED),
    paidPlansEnabled: parseBoolean(process.env.NUXT_PUBLIC_PAID_PLANS_ENABLED),
    customDomainsEnabled: parseBoolean(process.env.NUXT_PUBLIC_CUSTOM_DOMAINS_ENABLED),
    featureBaseOrganization: process.env.NUXT_PUBLIC_FEATURE_BASE_ORGANISATION || null,
    selfHosted: parseBoolean(process.env.NUXT_PUBLIC_SELF_HOSTED, true),

    // Config within public will be also exposed to the client
    SENTRY_DSN_PUBLIC: process.env.SENTRY_DSN_PUBLIC,
    SENTRY_TRACES_SAMPLE_RATE: parseNumber(process.env.SENTRY_TRACES_SAMPLE_RATE),
    SENTRY_REPLAY_SAMPLE_RATE: parseNumber(process.env.SENTRY_REPLAY_SAMPLE_RATE),
    SENTRY_ERROR_REPLAY_SAMPLE_RATE: parseNumber(process.env.SENTRY_ERROR_REPLAY_SAMPLE_RATE),
  },

  /**
   * Used to authenticate that the requests are coming from the server - not from a client.
   */
  apiSecret: process.env.NUXT_API_SECRET || '',
  privateApiBase: process.env.NUXT_PRIVATE_API_BASE || null,
}
