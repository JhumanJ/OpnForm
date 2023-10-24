import { defineAsyncComponent } from 'vue'
import { registerComponents as registerCommonComponents } from './common'
import { registerComponents as registerFormComponents } from './forms'

import Child from './Child.vue'
import Modal from './Modal.vue'
import Loader from './common/Loader.vue'

export function registerComponents (app) {
  [Child, Modal, Loader].forEach(Component => {
    app.component(Component.name, Component)
  })

  registerCommonComponents(app)
  registerFormComponents(app)

  // Register async components
  app.component('FormEditor', defineAsyncComponent(() =>
    import('./open/forms/components/FormEditor.vue')
  ))
  app.component('NotionPage', defineAsyncComponent(() =>
    import('./open/NotionPage.vue')
  ))
}
