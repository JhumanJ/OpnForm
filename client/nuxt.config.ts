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
        '@nuxtjs/sitemap',
        '@nuxt/ui', 
        'nuxt-utm', 
        '@nuxtjs/i18n',
        '@nuxt/icon', 
        ...process.env.NUXT_PUBLIC_GTM_CODE ? ['@zadigetvoltaire/nuxt-gtm'] : [],
    ],
    build: {
        transpile: ["vue-notion", "query-builder-vue-3", "vue-signature-pad"],
    },
    i18n: {
        locales: [
          { code: 'en', name: 'English', iso: 'en-US', file: 'en.json' },
          { code: 'fr', name: 'French', iso: 'fr-FR', file: 'fr.json' },
          { code: 'hi', name: 'Hindi', iso: 'hi-IN', file: 'hi.json' },
          { code: 'es', name: 'Spanish', iso: 'es-ES', file: 'es.json' },
          { code: 'ar', name: 'Arabic', iso: 'ar-EG', file: 'ar.json' },
          { code: 'zh', name: 'Chinese', iso: 'zh-CN', file: 'zh.json' },
          { code: 'ja', name: 'Japanese', iso: 'ja-JP', file: 'ja.json' },
          { code: 'bn', name: 'Bengali', iso: 'bn-BD', file: 'bn.json' },
          { code: 'pt', name: 'Portuguese', iso: 'pt-BR', file: 'pt.json' },
          { code: 'ru', name: 'Russian', iso: 'ru-RU', file: 'ru.json' },
          { code: 'ur', name: 'Urdu', iso: 'ur-PK', file: 'ur.json' },
          { code: 'pa', name: 'Punjabi', iso: 'pa-IN', file: 'pa.json' },
          { code: 'de', name: 'German', iso: 'de-DE', file: 'de.json' },
          { code: 'jv', name: 'Javanese', iso: 'jv-ID', file: 'jv.json' },
          { code: 'ko', name: 'Korean', iso: 'ko-KR', file: 'ko.json' },
          { code: 'vi', name: 'Vietnamese', iso: 'vi-VN', file: 'vi.json' },
          { code: 'te', name: 'Telugu', iso: 'te-IN', file: 'te.json' },
          { code: 'mr', name: 'Marathi', iso: 'mr-IN', file: 'mr.json' },
          { code: 'ta', name: 'Tamil', iso: 'ta-IN', file: 'ta.json' },
          { code: 'tr', name: 'Turkish', iso: 'tr-TR', file: 'tr.json' },
        ],
        defaultLocale: 'en',
        lazy: true,
        langDir: 'lang/',
        strategy: 'no_prefix'
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
            path: '~/components/forms',
            pathPrefix: false,
            global: true
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
    icon: {
        clientBundle: {
            scan: {
                globInclude: ['**/*.vue', '**/*.json'],
            },
        },
      },
    sitemap,
    runtimeConfig,
    gtm
})