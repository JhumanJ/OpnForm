<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <v-checkbox :id="id?id:name" v-model="compVal" :disabled="disabled?true:null" :name="name">
      <slot name="label">
        {{ label }} <span v-if="required" class="text-red-500 required-dot">*</span>
      </slot>
    </v-checkbox>

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
import VCheckbox from './components/VCheckbox.vue'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'CheckboxInput',

  components: { InputWrapper, VCheckbox },
  props: {
    ...inputProps
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  mounted () {
    if (!this.compVal) {
      this.compVal = false
    }
  }
}
</script>
