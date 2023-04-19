<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label, {'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <div v-if="help && helpPosition=='above_input'" class="flex mb-1">
      <small :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
    </div>
    <textarea :id="id?id:name" v-model="compVal" :disabled="disabled"
              :class="[theme.default.input,{ '!ring-red-500 !ring-2': hasValidation && form.errors.has(name), '!cursor-not-allowed !bg-gray-200':disabled }]"
              class="resize-y"
              :name="name" :style="inputStyle"
              :placeholder="placeholder"
              :maxlength="maxCharLimit"
    />
    <div v-if="(help && helpPosition=='below_input') || showCharLimit" class="flex">
      <small v-if="help && helpPosition=='below_input'" :class="theme.default.help" class="flex-grow">
        <slot name="help"><span class="field-help" v-html="help" /></slot>
      </small>
      <small v-else class="flex-grow"></small>
      <small v-if="showCharLimit && maxCharLimit" :class="theme.default.help">
      {{ charCount }}/{{ maxCharLimit }}
      </small>
    </div>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input.js'

export default {
  name: 'TextAreaInput',
  mixins: [inputMixin],

  props: {
    maxCharLimit: { type: Number, required: false, default: null },
    showCharLimit: { type: Boolean, required: false, default: false },
  },
  computed: {
    charCount() {
      return (this.compVal) ? this.compVal.length : 0
    }
  }
}
</script>
