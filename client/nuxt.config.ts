// https://nuxt.com/docs/api/configuration/nuxt-config
import runtimeConfig from "./runtimeConfig";
import { sentryVitePlugin } from "@sentry/vite-plugin";
import sitemap from "./sitemap";

export default defineNuxtConfig({
    loglevel: process.env.NUXT_LOG_LEVEL || 'info',
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt',
        'nuxt3-notifications',
        'nuxt-simple-sitemap',
        '@nuxt/image',
        ... process.env.NUXT_PUBLIC_GOOGLE_ANALYTICS_CODE ? ['nuxt-gtag'] : [],
    ],
    build: {
        transpile: ["vue-notion", "query-builder-vue-3","vue-signature-pad"],
    },
    postcss: {
        plugins: {
            'postcss-import': {},
            'tailwindcss/nesting': {},
            tailwindcss: {},
            autoprefixer: {},
        },
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
        '~/components',
    ],
    nitro: {
        awsAmplify: {
            imageOptimization: {
                cacheControl: "public, max-age=600, immutable" // 10 minutes
            },
            imageSettings: {
                formats: ['image/webp'],
                dangerouslyAllowSVG: true,
            }
        }
    },
    image: runtimeConfig.public.useDummyImageProvider ? {
        provider: 'dummy',
        providers: {
            dummy: {
                provider: '~/lib/images/dummy-image-provider.js',
            }
        }
    } :{
        quality: 95,
        format: 'webp',
        domains: ['images.unsplash.com']
    },
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
    },
    sitemap,
    runtimeConfig
})
