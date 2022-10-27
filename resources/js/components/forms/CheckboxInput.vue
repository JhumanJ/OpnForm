<template>
  <div :class="wrapperClass">
    <v-checkbox :id="id?id:name" v-model="compVal" :disabled="disabled" :name="name" @input="$emit('input',$event)">
      <slot name="label">
        {{ label }} <span v-if="required" class="text-red-500 required-dot">*</span>
      </slot>
    </v-checkbox>
    <small v-if="help" :class="theme.default.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input'

import VCheckbox from './components/VCheckbox'
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
