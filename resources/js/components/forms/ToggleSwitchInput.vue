<template>
    <div :class="wrapperClass">
      <div class="flex">
        <v-switch :id="id?id:name" v-model="compVal" class="inline-block mr-2" :disabled="disabled" :name="name" @input="$emit('input',$event)" />
        <span>{{ label }} <span v-if="required" class="text-red-500 required-dot">*</span></span>
      </div>
      <small v-if="help" :class="theme.default.help">
        <slot name="help">{{ help }}</slot>
      </small>
      <has-error v-if="hasValidation" :form="form" :field="name" />
    </div>
  </template>
  
  <script>
  import inputMixin from '~/mixins/forms/input'
  
  import VSwitch from './components/VSwitch'
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
  