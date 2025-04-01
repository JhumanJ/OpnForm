import * as Sentry from "@sentry/nuxt";
import { useAuthStore } from "~/stores/auth";
 
Sentry.init({
  dsn: useRuntimeConfig().public.SENTRY_DSN_PUBLIC ?? null,
  
  // Setting this option to true will print useful information to the console while you're setting up Sentry.
  debug: false,
});
