import './common'
import './forms'

import Vue from 'vue'
import Child from './Child'
import Modal from './Modal'

import Loader from './common/Loader'

// Components that are registered globaly.
[
  Child,
  Modal,
  Loader
].forEach(Component => {
  Vue.component(Component.name, Component)
})
