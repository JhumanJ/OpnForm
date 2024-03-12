<template>
  <input-wrapper
    v-bind="inputWrapperProps"
  >
    <template #label>
      <slot name="label" />
    </template>

    <textarea :id="id?id:name" v-model="compVal" :disabled="disabled?true:null"
              :class="[theme.default.input,{ '!ring-red-500 !ring-2 !border-transparent': hasError, '!cursor-not-allowed !bg-gray-200':disabled }]"
              class="resize-y"
              :name="name" :style="inputStyle"
              :placeholder="placeholder"
              :maxlength="maxCharLimit"
    />

    <template v-if="maxCharLimit && showCharLimit" #bottom_after_help>
      <small :class="theme.default.help">
        {{ charCount }}/{{ maxCharLimit }}
      </small>
    </template>

    <template #error>
      <slot name="error" />
    </template>
  </input-wrapper>
</template>

<script>
import { inputProps, useFormInput } from './useFormInput.js'
import InputWrapper from './components/InputWrapper.vue'

export default {
  name: 'TextAreaInput',
  components: { InputWrapper },
  mixins: [],

  props: {
    ...inputProps,
    maxCharLimit: { type: Number, required: false, default: null },
    showCharLimit: { type: Boolean, required: false, default: false }
  },

  setup (props, context) {
    return {
      ...useFormInput(props, context)
    }
  },

  computed: {
    charCount () {
      return (this.compVal) ? this.compVal.length : 0
    }
  }
}
</script>
