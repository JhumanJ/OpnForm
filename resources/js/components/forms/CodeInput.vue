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
      :class="[theme.CodeInput.input,{ '!ring-red-500 !ring-2': hasError, '!cursor-not-allowed !bg-gray-200':disabled }]"
    >
      <codemirror :id="id?id:name" v-model="compVal" :disabled="disabled?true:null"
                  :options="cmOptions"
                  :style="inputStyle" :name="name"
                  :placeholder="placeholder"
      />
    </div>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { codemirror } from 'vue-codemirror'
import 'codemirror/lib/codemirror.css'

import 'codemirror/mode/htmlmixed/htmlmixed.js'

import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'CodeInput',

  components: { InputWrapper, codemirror },
  props: {
    ...inputProps
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  data () {
    return {
      cmOptions: {
        // codemirror options
        tabSize: 4,
        mode: 'text/html',
        theme: 'default',
        lineNumbers: true,
        line: true
      }
    }
  }
}
</script>
