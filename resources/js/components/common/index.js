import Vue from 'vue'

import Dropdown from './Dropdown'
import Card from './Card'
import Button from './Button'
// Components that are registered globaly.
[
  Card,
  Button,
  Dropdown
].forEach(Component => {
  Vue.component(Component.name, Component)
})
