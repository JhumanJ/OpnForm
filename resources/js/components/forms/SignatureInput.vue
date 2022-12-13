<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.CodeInput.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>

    <VueSignaturePad ref="signaturePad" 
          class="border-2" height="150px" 
          :name="name" 
          :options="{ onEnd }" />
    <button class="float-right text-nt-blue" @click="clear">Clear</button>

    <small v-if="help" :class="theme.CodeInput.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import Vue from 'vue'
import VueSignaturePad from 'vue-signature-pad'
import inputMixin from '~/mixins/forms/input'

Vue.use(VueSignaturePad)

export default {
  name: 'SignatureInput',

  components: {},
  mixins: [inputMixin],

  methods: {
    clear () {
      this.$refs.signaturePad.clearSignature()
      this.onEnd()
    },
    onEnd () {
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
      this.$set(this.form, this.name, (!isEmpty && data) ? data : null)
    }
  }
}
</script>
