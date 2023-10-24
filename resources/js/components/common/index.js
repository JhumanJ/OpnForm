import Dropdown from './Dropdown.vue'
import Card from './Card.vue'
import Button from './Button.vue'
export function registerComponents (app) {
  [
    Card,
    Button,
    Dropdown
  ].forEach(Component => {
    app.component(Component.name, Component)
  })
}
