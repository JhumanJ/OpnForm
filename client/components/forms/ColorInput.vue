<template>
  <input-wrapper v-bind="inputWrapperProps">
    <template #label>
      <span />
    </template>

    <div class="flex items-center">
      <input :id="id?id:name" v-model="compVal" :disabled="disabled?true:null"
             type="color" class="mr-2"
             :name="name"
      >
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
import InputWrapper from './components/InputWrapper.vue'
import { inputProps, useFormInput } from './useFormInput.js'

export default {
  name: 'ColorInput',
  components: { InputWrapper },

  props: {
    ...inputProps
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  }
}
</script>
