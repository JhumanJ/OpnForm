// https://nuxt.com/docs/api/configuration/nuxt-config
import runtimeConfig from "./runtimeConfig"
import {sentryVitePlugin} from "@sentry/vite-plugin"
import sitemap from "./sitemap"
import gtm from "./gtm"

export default defineNuxtConfig({
    loglevel: process.env.NUXT_LOG_LEVEL || 'info',
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt',
        'nuxt-simple-sitemap',
        '@nuxt/ui',
        ...process.env.NUXT_PUBLIC_GTM_CODE ? ['@zadigetvoltaire/nuxt-gtm'] : [],
    ],
    build: {
        transpile: process.env.NODE_ENV === "development" ? [] : ["vue-notion", "query-builder-vue-3", "vue-signature-pad"],
    },
    experimental: {
        inlineRouteRules: true
    },
    sentry: {
        dsn: process.env.NUXT_PUBLIC_SENTRY_DSN,
        lazy: true,
    },
    gtag: {
        id: process.env.NUXT_PUBLIC_GOOGLE_ANALYTICS_CODE,
    },
    components: [
        {
            path: '~/components/forms',
            pathPrefix: false,
            global: true,
        },
        {
            path: '~/components/global',
            pathPrefix: false,
        },
        {
            path: '~/components/pages',
            pathPrefix: false,
        },
        {
            path: '~/components/open/integrations',
            pathPrefix: false,
            global: true,
        },
        '~/components',
    ],
    sourcemap: true,
    vite: {
        plugins: [
            // Put the Sentry vite plugin after all other plugins
            sentryVitePlugin({
                authToken: process.env.SENTRY_AUTH_TOKEN,
                org: "opnform",
                project: "opnform-vue",
            }),
        ],
        server: {
            hmr: {
                clientPort: 3000
            }
        }
    },
    tailwindcss: {
        cssPath: ['~/scss/app.scss']
    },
    colorMode: {
        preference: 'light',
        fallback: 'light',
        classPrefix: '',
    },
    ui: {
        icons: ['heroicons', 'material-symbols'],
    },
    sitemap,
    runtimeConfig,
    gtm
})
