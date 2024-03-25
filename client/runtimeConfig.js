export default {
  // Keys within public, will be also exposed to the client-side
  public: {
    apiBase: process.env.NUXT_PUBLIC_API_BASE ||'',
    appUrl: process.env.NUXT_PUBLIC_APP_URL || '',
    env: process.env.NUXT_PUBLIC_ENV || 'local',
    hCaptchaSiteKey: process.env.NUXT_PUBLIC_H_CAPTCHA_SITE_KEY || null,
    googleAnalyticsCode: process.env.NUXT_PUBLIC_GOOGLE_ANALYTICS_CODE || null,
    amplitudeCode: process.env.NUXT_PUBLIC_AMPLITUDE_CODE || null,
    crispWebsiteId: process.env.NUXT_PUBLIC_CRISP_WEBSITE_ID || null,
    aiFeaturesEnabled: process.env.NUXT_PUBLIC_AI_FEATURES_ENABLED || false,
    s3Enabled: process.env.NUXT_PUBLIC_S3_ENABLED || false,
    paidPlansEnabled: process.env.NUXT_PUBLIC_PAID_PLANS_ENABLED || false,
    customDomainsEnabled: process.env.NUXT_PUBLIC_CUSTOM_DOMAINS_ENABLED || false,
    featureBaseOrganization: process.env.NUXT_PUBLIC_FEATURE_BASE_ORGANISATION || null,

    // Config within public will be also exposed to the client
    SENTRY_DSN_PUBLIC: process.env.SENTRY_DSN_PUBLIC,
    SENTRY_TRACES_SAMPLE_RATE: parseFloat(process.env.SENTRY_TRACES_SAMPLE_RATE ?? '0'),
    SENTRY_REPLAY_SAMPLE_RATE: parseFloat(process.env.SENTRY_REPLAY_SAMPLE_RATE ?? '0'),
    SENTRY_ERROR_REPLAY_SAMPLE_RATE: parseFloat(process.env.SENTRY_ERROR_REPLAY_SAMPLE_RATE ?? '0'),
  },

  /**
   * Used to authenticate that the requests are coming from the server - not from a client.
   */
  apiSecret: process.env.NUXT_API_SECRET || '',
}
