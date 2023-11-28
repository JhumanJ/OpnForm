<template>
  <input-wrapper
    v-bind="$props"
  >
    <template #label>
      <slot name="label" />
    </template>

    <VueSignaturePad ref="signaturePad"
                     :class="[theme.default.input,{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]" height="150px"
                     :name="name"
                     :options="{ onEnd }"
    />

    <div class="flex">
      <small :class="theme.default.help">
        <a :class="theme.default.help" href="#" @click.prevent="clear">Clear</a>
      </small>
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
import InputWrapper from './components/InputWrapper.vue'
import VueSignaturePad from 'vue-signature-pad'

export default {
  name: 'SignatureInput',
  components: {InputWrapper, VueSignaturePad},

  props: {
    ...inputProps,
  },

  setup (props, context) {
    const {
      compVal,
      inputStyle,
      hasValidation,
      hasError
    } = useFormInput(props, context)

    return {
      compVal,
      inputStyle,
      hasValidation,
      hasError
    }
  },

  methods: {
    clear () {
      this.$refs.signaturePad.clearSignature()
      this.onEnd()
    },
    onEnd () {
      if (this.disabled) {
        this.$refs.signaturePad.clearSignature()
      } else {
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
        this.form[this.name] = (!isEmpty && data) ? data : null
      }
    }
  }
}
</script>
