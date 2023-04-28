<template>
  <div :class="wrapperClass">
    <input :id="id?id:name" v-model="compVal" :disabled="disabled"
           type="color"
           :name="name"
    >
    <label v-if="label" :for="id?id:name" class="text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <small v-if="help" :class="theme.default.help">
      <slot name="help"><span class="field-help" v-html="help" /></slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'ColorInput',
  mixins: [inputMixin]
}
</script>
