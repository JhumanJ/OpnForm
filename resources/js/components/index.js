import './common'
import './forms'

import Vue from 'vue'
import Child from './Child.vue'
import Modal from './Modal.vue'

import Loader from './common/Loader.vue'

// Components that are registered globaly.
[
  Child,
  Modal,
  Loader
].forEach(Component => {
  Vue.component(Component.name, Component)
})

// Lazy load some heavy component
Vue.component('FormEditor', () => import('./open/forms/components/FormEditor.vue'))
Vue.component('NotionPage', () => import('./open/NotionPage.vue'))
