import { createApp, configureCompat, ref } from 'vue'
import store from '~/store'
import router from '~/router'
import App from '~/components/App.vue'
import Base from './base.js'
import registerPlugin from './plugins'
import { registerComponents } from './components'

import '~/plugins'
import '~/components'

import '../sass/app.scss'

const app = createApp(App)
  .use(router)
  .use(store)
  .mixin(Base)

registerPlugin(app)
registerComponents(app)

configureCompat({
  // default everything to Vue 2 behavior
  MODE: 2,
  GLOBAL_MOUNT: false,
  COMPONENT_V_MODEL: false,
  INSTANCE_SET: false,
  INSTANCE_DELETE: false
})

router.app = app
router.isReady().then(() => app.mount('#app'))

export default app
