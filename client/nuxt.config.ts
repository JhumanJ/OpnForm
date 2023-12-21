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

const cachedRoutes = [
    '/',
    '/ai-form-builder',
    '/login',
    '/register',
    '/privacy-policy',
    '/terms-conditions',
].reduce((acc, curr) => (acc[curr] = {swr: 60 * 60}, acc), {});

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
    components: [
        {
            path: '~/components/forms',
            pathPrefix: false,
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
    routeRules: { ... cachedRoutes},
    devServer: {
        https: true,
    }
})
