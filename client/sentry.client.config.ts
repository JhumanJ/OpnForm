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
      const errorType = event.exception.values[0]?.type || '';
      const errorValue = event.exception.values[0]?.value || '';
      
      // Don't send validation exceptions to Sentry
      if (
        errorType === 'FetchError' &&
        (errorValue.includes('422') || errorValue.includes('401'))
      ) {
        return null
      }
      
      // Filter out chunk loading errors
      if (
        errorValue.includes('Failed to fetch dynamically imported module') ||
        errorValue.includes('Loading chunk') ||
        errorValue.includes('Failed to load resource')
      ) {
        return null
      }
    }

    const authStore = useAuthStore()
    if (authStore.check) {
      const user = authStore.user as { id?: string; email?: string } | null
      Sentry.setUser({
        id: user?.id,
        email: user?.email
      })
      event.user = {
        id: user?.id,
        email: user?.email
      }
    }
    return event
  }
});
