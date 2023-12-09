// https://nuxt.com/docs/api/configuration/nuxt-config

import opnformConfig from "./opnform.config";

const modules = [
    '@pinia/nuxt',
    '@vueuse/nuxt',
    '@vueuse/motion/nuxt'
]

if (opnformConfig.sentry_dsn) {
    modules.push('@nuxtjs/sentry')
}
export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt'
    ],
    postcss: {
        plugins: {
            'postcss-import': {},
            'tailwindcss/nesting': {},
            tailwindcss: {},
            autoprefixer: {},
        },
    },
    sentry: {
        dsn: opnformConfig.sentry_dsn,
    },
    routeRules: {
        '/ai-form-builder': {
            swr: 60*60
        },
        '/privacy-policy': {
            swr: 60*60
        },
        '/terms-conditions': {
            swr: 60*60
        },
    }
})
