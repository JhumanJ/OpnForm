import { defineAsyncComponent } from 'vue'

import HasError from './validation/HasError.vue'
import AlertError from './validation/AlertError.vue'
import AlertSuccess from './validation/AlertSuccess.vue'
import VCheckbox from './components/VCheckbox.vue'
import TextInput from './TextInput.vue'
import TextAreaInput from './TextAreaInput.vue'
import VSelect from './components/VSelect.vue'
import CheckboxInput from './CheckboxInput.vue'
import SelectInput from './SelectInput.vue'
import ColorInput from './ColorInput.vue'
import FileInput from './FileInput.vue'
import ImageInput from './ImageInput.vue'
import RatingInput from './RatingInput.vue'
import FlatSelectInput from './FlatSelectInput.vue'
import ToggleSwitchInput from './ToggleSwitchInput.vue'

export function registerComponents (app) {
  [
    HasError,
    AlertError,
    AlertSuccess,
    VCheckbox,
    VSelect,
    CheckboxInput,
    ColorInput,
    TextInput,
    SelectInput,
    TextAreaInput,
    FileInput,
    ImageInput,
    RatingInput,
    FlatSelectInput,
    ToggleSwitchInput
  ].forEach(Component => {
    Component.name ? app.component(Component.name, Component) : app.component(Component.name, Component)
  })

  // Register async components
  app.component('SignatureInput', defineAsyncComponent(() =>
    import('./SignatureInput.vue')
  ))
  app.component('RichTextAreaInput', defineAsyncComponent(() =>
    import('./RichTextAreaInput.vue')
  ))
  app.component('PhoneInput', defineAsyncComponent(() =>
    import('./PhoneInput.vue')
  ))
  app.component('DateInput', defineAsyncComponent(() =>
    import('./DateInput.vue')
  ))
}
