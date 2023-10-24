<template>
  <div :class="wrapperClass">
    <input-help v-if="help && helpPosition=='above_input'" :help="help" :theme="theme">
      <template #help>
        <slot name="help" />
      </template>
    </input-help>
    <div class="flex">
      <v-switch :id="id?id:name" v-model="compVal" class="inline-block mr-2" :disabled="disabled" />
      <slot name="label">
        <span>{{ label }} <span v-if="required" class="text-red-500 required-dot">*</span></span>
      </slot>
    </div>
    <input-help v-if="help && helpPosition=='below_input'" :help="help" :theme="theme">
      <template #help>
        <slot name="help" />
      </template>
    </input-help>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputHelp from './components/InputHelp.vue'
import VSwitch from './components/VSwitch.vue'
export default {
  name: 'ToggleSwitchInput',

  components: { InputHelp, VSwitch },
  props: {
    ...inputProps
  },

  setup (props, context) {
    const { compVal, inputStyle, hasValidation, hasError } = useFormInput(props, context)

    return {
      compVal,
      inputStyle,
      hasValidation,
      hasError
    }
  },

  mounted () {
    this.compVal = !!this.compVal
  }
}
</script>
