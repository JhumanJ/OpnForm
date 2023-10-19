import './axios'
import { registerLogEventOnApp } from './amplitude'
import './vapor'
import './sentry'

import Notifications from 'vue3-vt-notifications'
import { createMetaManager } from 'vue-meta'

function registerPlugin (app) {
  const metaManager = createMetaManager()

  app.use(Notifications)
  app.use(metaManager)
  registerLogEventOnApp(app)
  return app
}
export default registerPlugin
