// https://nuxt.com/docs/api/configuration/nuxt-config
import opnformConfig from "./opnform.config";
import sitemap from "./sitemap";

export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt',
        'nuxt3-notifications',
        'nuxt-simple-sitemap',
        // ... opnformConfig.sentry_dsn ? ['@nuxtjs/sentry'] : [],
    ],
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
        dsn: opnformConfig.sentry_dsn,
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
    sitemap
})
