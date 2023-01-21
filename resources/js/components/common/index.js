import Vue from 'vue'

import Dropdown from './Dropdown.vue'
import Card from './Card.vue'
import Button from './Button.vue'
// Components that are registered globaly.
[
  Card,
  Button,
  Dropdown
].forEach(Component => {
  Vue.component(Component.name, Component)
})
