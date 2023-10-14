import Notifications from 'vue3-vt-notifications'
import Meta from 'vue-meta'

function registerPlugin (app) {
  app.use(Notifications)
  app.use(Meta)
  return app
}
export default registerPlugin
