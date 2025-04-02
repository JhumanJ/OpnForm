import * as Sentry from "@sentry/nuxt";

Sentry.init({
  // If set up, you can use your runtime config here
  // dsn: useRuntimeConfig().public.sentry.dsn,
  dsn: useRuntimeConfig().public.SENTRY_DSN_PUBLIC ?? null,

  // This sets the sample rate to be 10%. You may want this to be 100% while
  // in development and sample at a lower rate in production
  replaysSessionSampleRate: 0.1,
  
  // If the entire session is not sampled, use the below sample rate to sample
  // sessions when an error occurs.
  replaysOnErrorSampleRate: 1.0,
  
  // If you don't want to use Session Replay, just remove the line below:
  integrations: [Sentry.replayIntegration()],
  
  // Setting this option to true will print useful information to the console while you're setting up Sentry.
  debug: false,

  // Ensure that source maps are properly handled
  attachStacktrace: true,

  beforeSend (event) {
    if (event.exception?.values?.length) {
      // Don't send validation exceptions to Sentry
      if (
        event.exception.values[0]?.type === 'FetchError'
        && (event.exception.values[0]?.value?.includes('422')
          || event.exception.values[0]?.value?.includes('401'))
      )
        return null
    }
    const authStore = useAuthStore()
    if (authStore.check) {
      Sentry.setUser({
        id: authStore.user?.id,
        email: authStore.user?.email
      })
      event.user = {
        id: authStore.user?.id,
        email: authStore.user?.email
      }
    }
    return event
  }
});
