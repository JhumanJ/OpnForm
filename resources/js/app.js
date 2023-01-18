import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n.js'
import App from '~/components/App.vue'
import LoadScript from 'vue-plugin-load-script'
import Base from './base.js'

import '~/plugins'
import '~/components'

import '../sass/app.scss'

Vue.config.productionTip = false

Vue.mixin(Base)
Vue.use(LoadScript)

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  ...App
})
