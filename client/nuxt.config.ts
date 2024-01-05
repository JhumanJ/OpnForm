// https://nuxt.com/docs/api/configuration/nuxt-config
import runtimeConfig from "./runtimeConfig";
import opnformConfig from "./opnform.config";
import sitemap from "./sitemap";

export default defineNuxtConfig({
    site: {
        url: opnformConfig.app_url
    },
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt',
        'nuxt3-notifications',
        'nuxt-simple-sitemap',
        '@nuxt/image',
        // ... opnformConfig.sentry_dsn ? ['@nuxtjs/sentry'] : [],
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
    sitemap,
    runtimeConfig
})
