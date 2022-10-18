import Vue from 'vue'

import HasError from './validation/HasError.vue'
import AlertError from './validation/AlertError'
import AlertSuccess from './validation/AlertSuccess'
import VCheckbox from './components/VCheckbox'
import TextInput from './TextInput'
import TextAreaInput from './TextAreaInput'
import VSelect from './components/VSelect'
import CheckboxInput from './CheckboxInput'
import SelectInput from './SelectInput'
import ColorInput from './ColorInput'
import RichTextAreaInput from './RichTextAreaInput'
import FileInput from './FileInput'
import ImageInput from './ImageInput'
import DateInput from './DateInput';
import RatingInput from './RatingInput';
import FlatSelectInput from './FlatSelectInput';
import ToggleSwitchInput from './ToggleSwitchInput';

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
  RichTextAreaInput,
  DateInput,
  RatingInput,
  FlatSelectInput,
  ToggleSwitchInput
].forEach(Component => {
  Vue.component(Component.name, Component)
})
