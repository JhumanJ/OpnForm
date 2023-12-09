<template>
  <div :class="wrapperClass" :style="inputStyle">
    <slot name="label">
      <input-label v-if="label && !hideFieldName"
                   :label="label"
                   :theme="theme"
                   :required="required"
                   :native-for="id?id:name"
                   :uppercase-labels="uppercaseLabels"
      />
    </slot>
    <slot v-if="help && helpPosition==='above_input'" name="help">
      <input-help :help="help" :theme="theme" />
    </slot>
    <slot />

    <slot v-if="(help && helpPosition==='below_input') || $slots.bottom_after_help" name="help">
      <input-help :help="help" :theme="theme">
        <template #after-help>
          <slot name="bottom_after_help" />
        </template>
      </input-help>
    </slot>
    <slot name="error">
      <has-error v-if="hasValidation && form" :form="form" :field="name" />
    </slot>
  </div>
</template>

<script>
import InputLabel from './InputLabel.vue'
import InputHelp from './InputHelp.vue'

export default {
  name: 'InputWrapper',
  components: { InputLabel, InputHelp },

  props: {
    id: { type: String, required: false },
    name: { type: String, required: false },
    label: { type: String, required: false },
    form: { type: Object, required: false },
    theme: { type: Object, required: true },
    wrapperClass: { type: String, required: false },
    inputStyle: { type: Object, required: false },
    help: { type: String, required: false },
    helpPosition: { type: String, default: 'below_input' },
    uppercaseLabels: { type: Boolean, default: true },
    hideFieldName: { type: Boolean, default: true },
    required: { type: Boolean, default: false },
    hasValidation: { type: Boolean, default: true }
  }
}
</script>
