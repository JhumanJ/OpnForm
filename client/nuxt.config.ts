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

const preRenderedRoutes = [
    '/',
    '/ai-form-builder',
    '/login',
    '/register',
    // '/privacy-policy',
    // '/terms-conditions',
    '/templates',
    '/templates/*',
].reduce((acc, curr) => (acc[curr] = {prerender: true}, acc), {});

export default defineNuxtConfig({
    devtools: {enabled: true},
    css: ['~/scss/app.scss'],
    modules: [
        '@pinia/nuxt',
        '@vueuse/nuxt',
        '@vueuse/motion/nuxt',
        'nuxt3-notifications'
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
    routeRules: { ... preRenderedRoutes}
})
