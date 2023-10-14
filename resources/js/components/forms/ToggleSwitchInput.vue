<template>
    <div :class="wrapperClass">
      <small v-if="help && helpPosition=='above_input'" :class="theme.default.help" class="flex mb-1">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
      <div class="flex">
        <v-switch :id="id?id:name" v-model="compVal" class="inline-block mr-2" :disabled="disabled" :name="name" @input="$emit('input',$event)" />
        <slot name="label">
          <span>{{ label }} <span v-if="required" class="text-red-500 required-dot">*</span></span>
        </slot>
      </div>
      <small v-if="help && helpPosition=='below_input'" :class="theme.default.help">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
      <has-error v-if="hasValidation" :form="form" :field="name" />
    </div>
  </template>
  
  <script>
  import inputMixin from '~/mixins/forms/input.js'
  
  import VSwitch from './components/VSwitch.vue'
  export default {
    name: 'ToggleSwitchInput',
  
    components: { VSwitch },
    mixins: [inputMixin],
    props: {},
  
    mounted () {
      this.compVal = !!this.compVal
      this.$emit('input', !!this.compVal)
    }
  }
  </script>
  