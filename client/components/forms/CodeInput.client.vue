<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <template #help>
      <slot name="help" />
    </template>

    <div
      :class="[theme.CodeInput.input,{ '!ring-red-500 !ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-gray-200':disabled }]"
    >
      <codemirror :id="id?id:name" v-model="compVal" :disabled="disabled?true:null"
                  :extensions="extensions"
                  :style="inputStyle" :name="name" :tab-size="4"
                  :placeholder="placeholder"
      />
    </div>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { Codemirror } from 'vue-codemirror'

import {html} from '@codemirror/lang-html'

import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'

export default {
  components: { InputWrapper, Codemirror },
  props: {
    ...inputProps
  },

  setup (props, context) {
    const extensions = [html()]
    return {
      ...useFormInput(props, context),
      extensions
    }
  }
}
</script>
