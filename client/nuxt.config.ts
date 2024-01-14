// https://nuxt.com/docs/api/configuration/nuxt-config
import runtimeConfig from "./runtimeConfig";
import opnformConfig from "./opnform.config";
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
        ... process.env.NUXT_PUBLIC_SENTRY_DSN ? ['@nuxtjs/sentry'] : [],
    ],
    build: {
        transpile: ["vue-notion"],
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
    image: {
        quality: 95,
    },
    sitemap,
    runtimeConfig
})
