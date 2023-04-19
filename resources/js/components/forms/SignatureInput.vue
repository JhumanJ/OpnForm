<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.CodeInput.label,{'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <div v-if="help && helpPosition=='above_input'" class="flex mb-1">
      <small :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>

    <VueSignaturePad ref="signaturePad"
                     :class="[theme.default.input,{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]" height="150px"
                     :name="name"
                     :options="{ onEnd }"
    />

    <div class="flex">
      <small v-if="help && helpPosition=='below_input'" :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
      <small v-else class="flex-grow" />
      <small :class="theme.default.help">
        <a :class="theme.default.help" href="#" @click.prevent="clear">Clear</a>
      </small>
    </div>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import Vue from 'vue'
import VueSignaturePad from 'vue-signature-pad'
import inputMixin from '~/mixins/forms/input.js'

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
      if(this.disabled){
        this.$refs.signaturePad.clearSignature()
      }else{
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
        this.$set(this.form, this.name, (!isEmpty && data) ? data : null)
      }
    }
  }
}
</script>
