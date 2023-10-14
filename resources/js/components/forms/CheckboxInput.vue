<template>
  <div :class="wrapperClass">
    <small v-if="help && helpPosition=='above_input'" :class="theme.default.help" class="flex mb-1">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <v-checkbox :id="id?id:name" v-model="compVal" :disabled="disabled" :name="name" @input="$emit('input',$event)">
      <slot name="label">
        {{ label }} <span v-if="required" class="text-red-500 required-dot">*</span>
      </slot>
    </v-checkbox>
    <small v-if="help && helpPosition=='below_input'" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'

import VCheckbox from './components/VCheckbox.vue'
export default {
  name: 'CheckboxInput',

  components: { VCheckbox },
  mixins: [inputMixin],
  props: {},

  mounted () {
    this.compVal = !!this.compVal
    this.$emit('input', !!this.compVal)
  }
}
</script>
