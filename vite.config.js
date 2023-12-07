import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import { sentryVitePlugin } from '@sentry/vite-plugin'

const plugins = [
  laravel({
    input: [
      'resources/js/app.js'
    ],
    refresh: true
  }),
  vue({
    template: {
      transformAssetUrls: {
        base: null,
        includeAbsolute: false
      },
      compilerOptions: {
        compatConfig: {
          MODE: 2
        }
      }
    }
  })
]

if (false && process.env.SENTRY_AUTH_TOKEN) {
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
      'vt-notifications'
    ]
  },
  resolve: {
    alias: {
      '~': '/resources/js',
      '@': '/resources',
      vue: '@vue/compat'
    }
  },
  server: {
    hmr: {
      host: 'localhost',
      protocol: 'ws'
    }
  }
})
