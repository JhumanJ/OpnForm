import * as Sentry from "@sentry/vue";

function getSentryIntegrations() {
  // don't load on server
  if (!import.meta.client) return [];

  const router = useRouter();
  const browserTracing = new Sentry.BrowserTracing({
    routingInstrumentation: Sentry.vueRouterInstrumentation(router),
  });

  return [browserTracing];
}


export default defineNuxtPlugin({
  name: 'sentry',
  parallel: true,
  async setup(nuxtApp) {
    const vueApp = nuxtApp.vueApp;

    const config = useRuntimeConfig();

    Sentry.init({
      app: vueApp,
      dsn: config.public.SENTRY_DSN_PUBLIC ?? null,
      integrations: getSentryIntegrations(),

      // Set tracesSampleRate to 1.0 to capture 100%
      // of transactions for performance monitoring.
      // We recommend adjusting this value in production
      tracesSampleRate: config.public.SENTRY_TRACES_SAMPLE_RATE,

      // Set `tracePropagationTargets` to control for which URLs distributed tracing should be enabled
      // tracePropagationTargets: ["localhost", /^https:\/\/yourserver\.io\/api/],

      // This sets the sample rate. You may want this to be 100% while
      // in development and sample at a lower rate in production
      replaysSessionSampleRate: config.public.SENTRY_REPLAY_SAMPLE_RATE,

      // If the entire session is not sampled, use the below sample rate to sample
      // sessions when an error occurs.
      replaysOnErrorSampleRate: config.public.SENTRY_ERROR_REPLAY_SAMPLE_RATE,

      beforeSend(event) {
        if (event.exception.values.length) {
          // Don't send validation exceptions to Sentry
          if (event.exception.values[0].type === 'FetchError' &&
            (event.exception.values[0].value.includes('422') || event.exception.values[0].value.includes('401'))
          ) {
            return null
          }
        }
        return event;
      },
    })
  }
});
