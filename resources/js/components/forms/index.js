import Vue from 'vue'

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

// Components that are registered globaly.
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
  Vue.component(Component.name, Component)
})

// Lazy load some heavy component
Vue.component('SignatureInput', () => import('./SignatureInput.vue'))
Vue.component('RichTextAreaInput', () => import('./RichTextAreaInput.vue'))
Vue.component('DateInput', () => import('./DateInput.vue'))
Vue.component('PhoneInput', () => import('./PhoneInput.vue'))
