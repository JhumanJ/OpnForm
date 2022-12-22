import Vue from 'vue'

import PortalVue from 'portal-vue'

import Notifications from "vt-notifications"

Vue.use(PortalVue)
Vue.use(Notifications)

Vue.prototype.$getCrisp = () => {
  return window.$crisp
}
