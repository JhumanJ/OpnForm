const path = require('path')
const fs = require('fs-extra')
const mix = require('laravel-mix')
require('laravel-mix-versionhash')
const tailwindcss = require('tailwindcss')

const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer')

mix
  .js('resources/js/app.js', 'public/dist/js').vue()
  .sass('resources/sass/app.scss', 'public/dist/css')
  .options({
    processCssUrls: false,
    postCss: [tailwindcss('./tailwind.config.js')]
  })
  .disableNotifications()
  .extract(['vue', 'vue-router', 'vuex', 'axios', 'vue-meta',
    'vue-i18n', 'portal-vue'])

if (mix.inProduction()) {
  mix
    // .extract() // Disabled until resolved: https://github.com/JeffreyWay/laravel-mix/issues/1889
    // .version() // Use `laravel-mix-versionhash` for the generating correct Laravel Mix manifest file.
    .versionHash()

  // Vapor config
  const ASSET_URL = process.env.ASSET_URL + '/'
  mix.webpackConfig(webpack => {
    return {
      plugins: [
        new webpack.DefinePlugin({
          'process.env.ASSET_PATH': JSON.stringify(ASSET_URL)
        })
      ],
      output: {
        publicPath: ASSET_URL
      }
    }
  })
} else {
  mix.sourceMaps()
}

mix.webpackConfig({
  plugins: [
    new BundleAnalyzerPlugin()
  ],
  resolve: {
    extensions: ['.js', '.json', '.vue'],
    alias: {
      '~': path.join(__dirname, './resources/js')
    }
  },
  output: {
    chunkFilename: 'dist/js/[chunkhash].js',
    path: path.resolve(__dirname, mix.inProduction() ? './public/build' : './public')
  }
})

mix.then(() => {
  if (mix.inProduction()) {
    process.nextTick(() => publishAssets())
  }
})

function publishAssets () {
  const publicDir = path.resolve(__dirname, './public')

  fs.removeSync(path.join(publicDir, 'dist'))
  fs.copySync(path.join(publicDir, 'build', 'dist'), path.join(publicDir, 'dist'))
  fs.removeSync(path.join(publicDir, 'build'))
}
