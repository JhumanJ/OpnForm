import {defineConfig} from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'
import { sentryVitePlugin } from '@sentry/vite-plugin'

const plugins = [
  laravel({
    input: [
      'resources/js/app.js'
    ],
    valetTls: 'opnform.test'
  }),
  vue({
    template: {
      transformAssetUrls: {
        base: null,
        includeAbsolute: false
      }
    }
  })
]

if (process.env.SENTRY_AUTH_TOKEN) {
  plugins.push(sentryVitePlugin({
    org: 'opnform',
    project: 'opnform',
    authToken: process.env.SENTRY_AUTH_TOKEN
  }))
}

export default defineConfig({
  build: {
    sourcemap: process.env.SENTRY_AUTH_TOKEN ? true : 'inline'
  },
  esbuild: {
    minify: true,
    minifySyntax: true
  },
  plugins: plugins,
  optimizeDeps: {
    exclude: [
      'vt-notifications', 'vue-tailwind', 'vue-tailwind/dist/vue-tailwind.css'
    ]
  },
  resolve: {
    alias: {
      '~': '/resources/js',
      '@': '/resources'
    }
  }
})
