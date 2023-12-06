import './axios'
import { registerLogEventOnApp } from './amplitude'
import { MotionPlugin } from '@vueuse/motion'
import './vapor'
import './sentry'

import Notifications from 'vue3-vt-notifications'
import { createMetaManager } from 'vue-meta'

function registerPlugin (app) {
  const metaManager = createMetaManager()

  app.use(Notifications)
  app.use(metaManager)
  app.use(MotionPlugin)
  registerLogEventOnApp(app)
  return app
}
export default registerPlugin
