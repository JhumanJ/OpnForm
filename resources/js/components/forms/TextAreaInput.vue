<template>
  <div :class="wrapperClass">
    <label v-if="label" :for="id?id:name"
           :class="[theme.default.label, {'uppercase text-xs':uppercaseLabels, 'text-sm':!uppercaseLabels}]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 required-dot">*</span>
    </label>
    <textarea :id="id?id:name" v-model="compVal" :disabled="disabled"
              :class="[theme.default.input,{ 'ring-red-500 ring-2': hasValidation && form.errors.has(name) }]"
              class="resize-y"
              :name="name" :style="inputStyle"
              :placeholder="placeholder"
    />
    <small v-if="help" :class="theme.default.help">
      <slot name="help">{{ help }}</slot>
    </small>
    <has-error v-if="hasValidation" :form="form" :field="name" />
  </div>
</template>

<script>
import inputMixin from '~/mixins/forms/input'

export default {
  name: 'TextAreaInput',
  mixins: [inputMixin]
}
</script>
