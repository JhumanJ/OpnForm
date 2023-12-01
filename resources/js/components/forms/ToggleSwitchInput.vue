<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex">
      <v-switch :id="id?id:name" v-model="compVal" class="inline-block mr-2" :disabled="disabled?true:null" />
      <slot name="label">
        <span>{{ label }} <span v-if="required" class="text-red-500 required-dot">*</span></span>
      </slot>
    </div>

    <template #help>
      <slot name="help" />
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import VSwitch from './components/VSwitch.vue'
import InputWrapper from './components/InputWrapper.vue'
export default {
  name: 'ToggleSwitchInput',

  components: { InputWrapper, VSwitch },
  props: {
    ...inputProps
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  mounted () {
    this.compVal = !!this.compVal
  }
}
</script>
